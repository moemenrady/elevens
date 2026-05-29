<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Services\EmployeeFreeDrinkService;
use App\Services\IngredientStockService;

class EmployeeTransactionsController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | عرض صفحة إضافة عملية
    |--------------------------------------------------------------------------
    */




    public function create()
    {
        $employees = Employee::all();
        $products = Product::select('id', 'name', 'price',)->with('category')->get();

        return view('employee_transactions.create', compact('employees', 'products'));
    }

    /*
    |--------------------------------------------------------------------------
    | عرض كل العمليات
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $query = EmployeeTransaction::with(['employee', 'product']);

        // فلترة بالاسم
        if ($request->filled('search')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        // فلترة بنوع العملية
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // فلترة بالتاريخ (من - إلى)
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->latest()->paginate(15);

        return view('employee_transactions.index', compact('transactions'));
    }

    /*
    |--------------------------------------------------------------------------
    | حفظ عملية جديدة
    |--------------------------------------------------------------------------
    */

    public function store(
        Request $request,
        EmployeeFreeDrinkService $freeDrinkService,
        IngredientStockService $ingredientStockService
    ) {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'items' => 'required|array|min:1',

            'items.*.type' => 'required|in:purchase,advance',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.quantity' => 'nullable|integer|min:1',
            'items.*.amount' => 'nullable|numeric|min:0',
            'items.*.note' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {

            $employee = Employee::findOrFail($validated['employee_id']);

            $items = [];

            foreach ($validated['items'] as $item) {

                $product = null;

                if (!empty($item['product_id'])) {
                    $product = Product::findOrFail($item['product_id']);
                }

                $items[] = [
                    'type'       => $item['type'],
                    'product_id' => $product?->id,
                    'price'      => $product?->price ?? 0,
                    'quantity'   => $item['quantity'] ?? 1,
                    'amount'     => $item['amount'] ?? 0,
                    'note'       => $item['note'] ?? null,
                    'product'    => $product, // 👈 مهم للمخزون
                ];
            }

            // 🔥 تطبيق free drink logic
            $calculated = $freeDrinkService->calculateItems($employee, $items);

            foreach ($calculated as $item) {

                // =========================
                // 🛒 PURCHASE (خصم مخزون + خامات)
                // =========================
                if ($item['type'] === 'purchase' && $item['product_id']) {

                    $product = Product::findOrFail($item['product_id']);

                    // 🔥 خصم من المخزون أو الخامات
                    if ($product->is_produced) {
                        $ingredientStockService->deductProductIngredients(
                            $product,
                            $item['quantity']
                        );
                    } else {
                        $this->deductProductQuantity($product, $item['quantity']);
                    }
                }

                // =========================
                // 💰 ADVANCE (بدون مخزون)
                // =========================
                if ($item['type'] === 'advance') {
                    // لا شيء في المخزون
                }

                // =========================
                // 💾 حفظ الترانزاكشن
                // =========================
                EmployeeTransaction::create([
                    'employee_id' => $employee->id,
                    'product_id'  => $item['product_id'],
                    'quantity'    => $item['quantity'],
                    'amount'      => $item['amount'],
                    'type'        => $item['type'],
                    'notes'       => $item['note'],
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل العمليات مع تحديث المخزون بنجاح',
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    protected function deductProductQuantity(Product $product, $qty)
    {
        // 🔥 لو المنتج مُصنَّع
        if ($product->is_produced) {

            $missingIngredients = [];

            // تأكد من تحميل العلاقة
            $product->loadMissing('ingredients');

            foreach ($product->ingredients as $ingredient) {

                // الكمية المطلوبة = amount في pivot × الكمية المطلوبة من المنتج
                $requiredAmount = $ingredient->pivot->amount * $qty;

                // المخزون الحالي في جدول ingredients هو stock
                if ($ingredient->stock < $requiredAmount) {
                    $missingIngredients[] = $ingredient->name;
                }
            }

            // لو فيه خامات ناقصة
            if (!empty($missingIngredients)) {

                $list = implode('، ', $missingIngredients);

                throw new \Exception(
                    "⚠️ المنتج \"{$product->name}\" يحتاج الخامات التالية: {$list}"
                );
            }

            // ✅ خصم الخامات لو متوفرة
            foreach ($product->ingredients as $ingredient) {

                $requiredAmount = $ingredient->pivot->amount * $qty;

                $ingredient->decrement('stock', $requiredAmount);
            }

            return;
        }

        // 🔹 لو منتج عادي
        if ($product->quantity < $qty) {

            $available = $product->quantity;
            $requested = $qty;
            $name = $product->name;

            if ($available <= 0) {
                throw new \Exception("❌ المنتج \"{$name}\" غير متوفر حالياً في المخزون.");
            }

            throw new \Exception(
                "❌ المنتج \"{$name}\" المطلوب {$requested} – المتاح فقط {$available}."
            );
        }

        $product->decrement('quantity', $qty);
    }
    /*
    |--------------------------------------------------------------------------
    | عرض عملية واحدة
    |--------------------------------------------------------------------------
    */

    public function show(EmployeeTransaction $employee_transaction)
    {
        return view(
            'employees.transaction_show',
            compact('employee_transaction')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | حذف عملية
    |--------------------------------------------------------------------------
    */

    public function destroy(EmployeeTransaction $employee_transaction)
    {
        $employee_transaction->delete();

        return redirect()
            ->back()
            ->with('success', 'تم حذف العملية');
    }
    public function freeDrinkStatus($id, EmployeeFreeDrinkService $service)
    {
        $employee = Employee::findOrFail($id);

        return response()->json([
            'employee_id' => $employee->id,
            'has_free_today' => $service->canTakeFree($employee),
            'used_today' => $service->freeUsedToday($employee),
        ]);
    }
}
