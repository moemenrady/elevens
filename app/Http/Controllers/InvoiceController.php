<?php

// app/Http/Controllers/InvoiceController.php
namespace App\Http\Controllers;

use App\Enums\SystemActionType;
use App\Http\Requests\StoreInvoiceRequest;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Shift;
use App\Models\ShiftAction;
use App\Services\ShiftService;
use App\Support\InvoiceNumber;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\SystemAction;
use App\Models\VariantStock;
use Schema;

class InvoiceController extends Controller
{

  public function index()
  {
    $invoices = Invoice::with('items')->get();
    return view('invoices.index', compact('invoices'));
  }

  public function show(Invoice $invoice)
  {
    // صلاحيات: عرض الفاتورة للأدمن أو صاحب الفاتورة أو حسب policy


    // eager load relations: items, customer/user, any linked models
    $invoice->load(['items', 'user']); // تأكد أن relations موجودة

    // حسابات ملخص
    $total = $invoice->items->sum('total');
    $cost = $invoice->items->sum('cost') * 1; // مجموع تكلفة الوحدات (cost * qty إذا مخزن غير مضروب)
    // إذا لديك cost مخزن لكل item كمجموع، عدّل حسب الحاجة
    $profit = $total - $cost;

    return view('invoices.show', compact('invoice', 'total', 'cost', 'profit'));
  }
  // public function admin_show(Invoice $invoice)
  //   {
  //   $invoice->load(['items', 'user']); // تأكد أن relations موجودة

  //     // حسابات ملخص
  //     $total = $invoice->items->sum('total');
  //     $cost = $invoice->items->sum('cost') * 1; // مجموع تكلفة الوحدات (cost * qty إذا مخزن غير مضروب)
  //     // إذا لديك cost مخزن لكل item كمجموع، عدّل حسب الحاجة
  //     $profit = $total - $cost;

  //     return view('invoices.admin-show', compact('invoice', 'total', 'cost', 'profit'));

  //   }


  public function client_show(Invoice $invoice)
{
    // تحميل العلاقات الأساسية فقط (العميل وبنود الفاتورة)
    $invoice->load(['client', 'items']);

    // بما أن النظام أصبح منتجات فقط، كل البنود هي purchaseItems
    $purchaseItems = $invoice->items;

    // إجمالي الفاتورة (مخزن مسبقاً في جدول الفواتير)
    $totalAmount = $invoice->total;

    // تمرير البيانات الصافية التي تحتاجها صفحة الـ Blade
    return view('invoices.client_show', [
        'invoice'       => $invoice,
        'purchaseItems' => $purchaseItems,
        'totalAmount'   => $totalAmount,
    ]);
}

  public function ajaxSearch(Request $request)
  {
    $query = Invoice::query()->with('client')->where('total', '>', 0);

    if ($q = $request->query('q')) {
      $query->whereHas('client', fn($q2) => $q2->where('name', 'like', "%{$q}%"))
        ->orWhere('invoice_number', 'like', "%{$q}%");
    }

    if ($types = $request->query('types')) {
      $types = explode(',', $types);
      $query->whereIn('type', $types);
    }

    if ($from = $request->query('from')) {
      $query->whereDate('updated_at', '>=', $from);
    }
    if ($to = $request->query('to')) {
      $query->whereDate('updated_at', '<=', $to);
    }

    return $query->orderByDesc('updated_at')->limit(50)->get()->map(function ($inv) {

      return [

        'id' => $inv->id,
        'invoice_number' => $inv->invoice_number,
        'client_name' => $inv->client->name ?? null,
        'type' => $inv->type,
        'total' => $inv->total,
        'created_at' => $inv->created_at,
        'updated_at' => $inv->updated_at,
      ];
    });
  }


  // لو عايز المعاينة بدون حفظ (يعتمد على نفس فورم الداتا)
  public function preview(Request $request)
  {
    $rawItems = json_decode($request->input('items'), true);
    if (!$rawItems) return redirect()->back()->with('error', 'السلة فارغة');

    $processedItems = [];
    $grandTotal = 0;

    foreach ($rawItems as $item) {
      $variant = \App\Models\ProductVariant::where('product_id', $item['product_id'])
        ->where('color_id', $item['color_id'])
        ->where('size_id', $item['size_id'])
        ->first();

      $price = $variant->price ?? 0;
      $qty = (int) $item['quantity'];
      $total = $price * $qty;

      $processedItems[] = [
        'product_id' => $item['product_id'],
        'color_id'   => $item['color_id'], // مهم للإرسال لاحقاً
        'size_id'    => $item['size_id'],  // مهم للإرسال لاحقاً
        'color_name' => $item['color_name'],
        'size_name'  => $item['size_name'],
        'name'       => $item['name'] . ' (' . $item['color_name'] . ' - ' . $item['size_name'] . ')',
        'qty'        => $qty,
        'price'      => $price,
        'cost'       => $variant->cost ?? 0,
        'total'      => $total,
        'is_printed' => $item['is_printed'],
      ];
      $grandTotal += $total;
    }

    $type = $this->determineInvoiceType($processedItems);

    return view('sale_proccess.invoice', [
      'items'      => collect($processedItems),
      'type'       => $type,
      'grandTotal' => $grandTotal,
    ]);
  }

  /**
   * وظيفة مساعدة لتحديد نوع الفاتورة
   */
  private function determineInvoiceType($items)
  {
    $hasPrinted = collect($items)->contains('is_printed', 1);
    $hasPlain = collect($items)->contains('is_printed', 0);

    if ($hasPrinted && $hasPlain) return 'مختلطة';
    if ($hasPrinted) return 'مطبوعات';
    return 'سادة';
  }

  public function storeVariantStock(Request $request)
  {
    foreach ($request->items as $item) {
      $stock = VariantStock::whereHas('variant', function ($q) use ($item) {
        $q->where([
          'product_id' => $item['product_id'],
          'color_id'   => $item['color_id'],
          'size_id'    => $item['size_id'],
        ]);
      })->where('is_printed', $item['is_printed'])->first();

      $stock->decrement('quantity', $item['quantity']);
    }

    return response()->json(['success' => true]);
  }


public function store(StoreInvoiceRequest $request)
{
    $validated = $request->validated();
    $user = Auth::user();

    try {
        $invoice = DB::transaction(function () use ($validated, $user) {
            $totalInvoicePrice = 0;
            $totalInvoiceProfit = 0;
            $itemsToSave = [];

            foreach ($validated['items'] as $item) {
                // 1. جلب الـ Variant بناءً على المنتج واللون والمقاس
                $variant = ProductVariant::with('product')
                    ->where('product_id', $item['product_id'])
                    ->where('color_id', $item['color_id'] ?? null)
                    ->where('size_id', $item['size_id'] ?? null)
                    ->first();

                if (!$variant) {
                    throw new \RuntimeException("المنتج (اللون أو المقاس) غير متوفر في النظام", 422);
                }

                // 2. جلب المخزون من جدول variant_stocks بناءً على حالة الطباعة
                $isPrinted = filter_var($item['is_printed'] ?? false, FILTER_VALIDATE_BOOLEAN);
                
                $stock = VariantStock::where('product_variant_id', $variant->id)
                    ->where('is_printed', $isPrinted)
                    ->first();

                $qty = (int)($item['qty'] ?? 1);
                $availableQty = $stock ? $stock->quantity : 0;

                // 3. التحقق من توفر الكمية
                if ($availableQty < $qty) {
                    $statusName = $isPrinted ? 'مطبوع' : 'سادة';
                    throw new \RuntimeException(json_encode([
                        [
                            'product_name' => "{$variant->product->name} ({$item['color_name']} - {$item['size_name']}) [$statusName]", 
                            'required' => $qty, 
                            'available' => $availableQty
                        ]
                    ]), 422);
                }

                // 4. الحسابات المالية
                $price = $variant->price;
                $cost = $variant->cost;
                $totalRowPrice = $price * $qty;
                $totalRowProfit = ($price - $cost) * $qty;

                $itemsToSave[] = [
                    'item_type'  => 'product',
                    'product_id' => $item['product_id'],
                    'name'       => $variant->product->name,
                    'color_name' => $item['color_name'] ?? '',
                    'size_name'  => $item['size_name'] ?? '',
                    'is_printed' => $isPrinted,
                    'qty'        => $qty,
                    'price'      => $price,
                    'cost'       => $cost,
                    'total'      => $totalRowPrice,
                ];

                $totalInvoicePrice += $totalRowPrice;
                $totalInvoiceProfit += $totalRowProfit;

                // 5. خصم الكمية من جدول variant_stocks
                $stock->decrement('quantity', $qty);
            }

            // 6. إنشاء الفاتورة وحفظ العناصر (نفس كودك السابق)
            $invoice = Invoice::create([
                'invoice_number' => InvoiceNumber::next(),
                'client_id'      => $validated['client_id'] ?? null,
                'created_by'     => $user->id,
                'type'           => $this->determineInvoiceType($itemsToSave),
                'total'          => $totalInvoicePrice,
                'profit'         => $totalInvoiceProfit,
                'notes'          => $validated['notes'] ?? null,
            ]);

            foreach ($itemsToSave as $row) {
                $row['invoice_id'] = $invoice->id;
                InvoiceItem::create($row);
            }

            // تسجيل العملية
            SystemAction::create([
                'user_id' => $user->id,
                'action' => 'sale_process',
                'actionable_type' => Invoice::class,
                'actionable_id' => $invoice->id,
                'invoice_id' => $invoice->id,
                'amount' => $invoice->total,
                'note' => "إنشاء فاتورة بيع - #{$invoice->invoice_number}",
                'meta' => json_encode($itemsToSave),
                'ip' => request()->ip(),
            ]);

            return $invoice;
        });

        return response()->json([
            'status' => 'success',
            'message' => 'تم حفظ الفاتورة بنجاح',
            'invoice_id' => $invoice->id
        ]);

    } catch (\RuntimeException $e) {
        if ($e->getCode() === 422) {
            $decodedMessage = json_decode($e->getMessage(), true);
            return response()->json([
                'status' => 'error',
                'message' => 'عذراً، يوجد نقص في المخزون',
                'shortages' => is_array($decodedMessage) ? $decodedMessage : null,
                'error_detail' => !is_array($decodedMessage) ? $e->getMessage() : null
            ], 422);
        }
        throw $e;
    }
}


  private function buildItemsFromRequest(array $requestItems): array
  {
    $items = [];
    $grandTotal = 0.0;
    $grandProfit = 0.0;

    foreach ($requestItems as $in) {
      $type = $in['item_type'];

      if ($type === 'product') {
        $product = Product::findOrFail($in['product_id']);
        $qty = (int) ($in['qty'] ?? 1);
        $price = (float) $product->price; // ممكن تسمح override من $in['price'] لو عايز
        $cost = (float) $product->cost;

        $total = $price * $qty;
        $profit = ($price - $cost) * $qty;

        $items[] = [
          'item_type' => 'product',
          'product_id' => $product->id,
          'name' => $product->name,
          'qty' => $qty,
          'price' => $price,
          'cost' => $cost,
          'total' => $total,
        ];
      }

      $grandTotal += end($items)['total'];
      $grandProfit += (end($items)['price'] - end($items)['cost']) * end($items)['qty'];
    }

    return [$items, ['total' => round($grandTotal, 2), 'profit' => round($grandProfit, 2)]];
  }






  public function print(Request $request)
  {

    $items = collect($request->items)->map(fn($item) => [
      'qty' => $item['qty'],
      'name' => $item['name'],
      'total' => $item['qty'] * $item['price'],

    ]);
    return view('sale_proccess.print', compact('items'));
  }


  public function clientInvoices(Request $request, $clientId)
  {
    $client = Client::findOrFail($clientId);
    $invoice = Invoice::where('client_id', $client->id);
    $invoiceCount = $invoice->count();
    $invoiceTotal = $invoice->sum('total');
    $invoices = $invoice->select('id', 'type', 'invoice_number', 'total')->get();
    return view('clients.invoices', compact('invoiceCount', 'invoiceTotal', 'invoices', 'client'));
  }
}
