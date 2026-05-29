<?php

namespace App\Http\Controllers;

use App\Models\ExpenseType;
use Illuminate\Http\Request;

class ExpenseTypeController extends Controller
{

    public function index()
    {
        // جلب الأنواع مع المصاريف المرتبطة بها لحساب الإجمالي
        $expenseTypes = ExpenseType::with('transactions')->get();
        return view('dashboard.expense_types.index', compact('expenseTypes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'setter_name' => 'required|string|max:255',
        ]);

        $expenseType = ExpenseType::create($data);

        return  redirect()->back()->with([
            'success' => true,
            'message' => 'تم إضافة نوع المصروف بنجاح 💰',
            'data' => $expenseType
        ]);
    }

    public function update(Request $request, $id)
    {
        $type = ExpenseType::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'setter_name' => 'required|string|max:255',
        ]);

        $type->update($data);
        return  redirect()->back()->with([
            'success' => true,
            'message' => 'تم تحديث البيانات بنجاح ✅',
            'data' => $type
        ]);
    }

    public function destroy($id)
    {
        ExpenseType::findOrFail($id)->delete();
        return  redirect()->back()->with([
            'success' => true,
            'message' => 'تم حذف النوع بنجاح 🗑️'
        ]);
    }
}
