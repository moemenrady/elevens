<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Expense;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use App\Models\Capital;
use App\Models\ExpenseType;
use App\Models\Partner;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['partner', 'expenseType']);

        // فلتر الملاحظات
        if ($request->filled('note')) {
            $query->where('note', 'like', '%' . $request->note . '%');
        }

        // فلتر النوع (داخل/خارج)
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // فلتر تصنيف المصروف
        if ($request->filled('expense_type_id')) {
            $query->whereHas('expenseType', function ($q) use ($request) {
                $q->where('expense_type_id', $request->expense_type_id);
            });
        }

        // فلاتر التاريخ
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // جلب النتائج بعد الفلترة
        $transactions = $query->latest()->get();

        // الكود القديم الخاص بك لحساب المجاميع
        $filteredCapitalAdded = $transactions->where('type', 'in')->sum('amount');
        $filteredExpenses = $transactions->where('type', 'out')->sum('amount');

        // حساب رأس المال الإجمالي الكلي
        $totalCapital = Transaction::where('type', 'in')->sum('amount')
            - Transaction::where('type', 'out')->sum('amount');

        // ====== الكود الجديد الذي سنضيفه ======
        // تجميع إضافات رأس المال (لنتائج البحث) حسب اسم الشريك
        $groupedCapitalTransactions = $transactions->where('type', 'in')->groupBy(function ($tx) {
            return $tx->partner ? $tx->partner->name : 'شريك غير محدد';
        });
        // =======================================

        $expenseTypes = ExpenseType::all();
        $partners = Partner::all();
        return view('dashboard.transactions.index', compact(
            'transactions',
            'expenseTypes',
            'totalCapital',
            'filteredCapitalAdded',
            'filteredExpenses',
            'partners',
            'groupedCapitalTransactions' // لا تنسَ تمرير المتغير الجديد هنا
        ));
    }
    public function create()
    {
        $expenses = Expense::all();
        return view('dashboard.transactions.create', compact('expenses'));
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:in,out',
            'amount' => 'required|numeric|min:0.01',
            'expense_type_id' => 'required_if:type,out|exists:expense_types,id',
            'partner_id' => 'required_if:type,in|exists:partners,id',
            'note' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($data) {

            // 🧠 حساب الرصيد الحالي قبل العملية
            $currentBalance = Transaction::selectRaw("
            COALESCE(SUM(CASE 
                WHEN type = 'in' THEN amount 
                ELSE -amount 
            END), 0) as balance
        ")->value('balance');

            $before = $currentBalance;

            // 🟢 حالة ضخ رأس مال
            if ($data['type'] === 'in') {

                $after = $before + $data['amount'];

                // تسجيل في capitals
                Capital::create([
                    'amount' => $data['amount'],
                    'note'   => $data['note'] ?? null,
                ]);

                $transaction = Transaction::create([
                    'type' => 'in',
                    'amount' => $data['amount'],
                    'note' => $data['note'] ?? 'زيادة رأس المال',
                    'added_by' => auth()->id(),
                    'partner_id' => $data['partner_id'],
                    'balance_before' => $before,
                    'balance_after' => $after,
                ]);

                return redirect()->back()->with([
                    'success' => true,
                    'message' => 'تم إضافة رأس المال بنجاح 💰',
                    'data' => $transaction
                ]);
            }

            // 🔴 حالة مصروف
            if ($data['type'] === 'out') {

                $after = $before - $data['amount'];

                $transaction = Transaction::create([
                    'type' => 'out',
                    'amount' => $data['amount'],
                    'expense_type_id' => $data['expense_type_id'],
                    'note' => $data['note'] ?? 'مصروف',
                    'added_by' => auth()->id(),
                    'balance_before' => $before,
                    'balance_after' => $after,
                ]);

                return redirect()->back()->with([
                    'success' => true,
                    'message' => 'تم إضافة المصروف بنجاح 💰',
                    'data' => $transaction
                ]);
            }
        });
    }



    public function update(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);

        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'expense_type_id' => 'required_if:type,out|exists:expense_types,id',
            'note' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($transaction, $data) {

            // ✏️ تحديث البيانات
            $transaction->update([
                'amount' => $data['amount'],
                'expense_type_id' => $data['expense_type_id'] ?? null,
                'note' => $data['note'] ?? $transaction->note,
            ]);


            return redirect()->back()->with([
                'success' => true,
                'message' => 'تم تعديل العملية بنجاح ✏️',
                'data' => $transaction
            ]);
        });
    }
    public function destroy($id)
    {
        $transaction = Transaction::findOrFail($id)->delete();
        return redirect()->back()->with([
            'success' => true,
            'message' => 'تم حذف العملية بنجاح 💰',
            'data' => $transaction
        ]);
    }
}
