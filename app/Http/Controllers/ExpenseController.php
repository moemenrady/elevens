<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseType;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{


    public function index()
    {
        $expenses = Expense::with('type', 'user')->latest()->get();
        $types = ExpenseType::all(); // محتاجين الأنواع عشان دروب داون الإضافة والتعديل
        return view('dashboard.expenses.index', compact('expenses', 'types'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'expense_type_id' => 'required|exists:expense_types,id',
            'amount'          => 'required|numeric|min:0',
            'note'            => 'nullable|string|max:500',
        ]);

        $data['added_by'] = auth()->id() ?? 1; // نفترض المستخدم 1 لو مفيش Auth حالياً

        Expense::create($data);

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل المصروف بنجاح 💸'
        ]);
    }

    public function update(Request $request, $id)
    {
        $expense = Expense::findOrFail($id);

        $data = $request->validate([
            'expense_type_id' => 'required|exists:expense_types,id',
            'amount'          => 'required|numeric|min:0',
            'note'            => 'nullable|string|max:500',
        ]);

        $expense->update($data);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث المصروف بنجاح ✅'
        ]);
    }

    public function destroy($id)
    {
        Expense::findOrFail($id)->delete();
        return response()->json([
            'success' => true,
            'message' => 'تم حذف المصروف بنجاح 🗑️'
        ]);
    }
}
