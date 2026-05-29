<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Partner::create([
            'name' => $request->name,
            'percentage' => 0,
            'last_capital_snapshot' => 0, // قيمة افتراضية
        ]);

        return redirect()->back()->with('success', 'تم إضافة الشريك بنجاح');
    }

    public function update(Request $request, Partner $partner)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $partner->update([
            'name' => $request->name,
            'percentage' => $request->percentage,
        ]);

        return redirect()->back()->with('success', 'تم تعديل بيانات الشريك بنجاح');
    }

    public function destroy(Partner $partner)
    {
        $partner->delete();
        return redirect()->back()->with('success', 'تم حذف الشريك بنجاح');
    }
   public function calculate(Request $request)
{
    $request->validate([
        'total' => 'required|numeric|min:0.01',
        'calculation_type' => 'required|in:amount,percentage',
        'values' => 'required|array'
    ]);

    $total = $request->total;
    $values = $request->values;
    $type = $request->calculation_type;

    // 1. التحقق من صحة الإجمالي بناءً على النوع
    if ($type === 'amount') {
        // لو مبالغ، لازم مجموع المبالغ = الإجمالي
        if (abs(array_sum($values) - $total) > 0.01) { // استخدام abs لتفادي مشاكل الكسور البسيطة
            return back()->with('error', 'مجموع مبالغ الشركاء لا يساوي إجمالي رأس المال المدخل ❌');
        }
    } else {
        // لو نسب، لازم مجموع النسب = 100
        if (abs(array_sum($values) - 100) > 0.01) {
            return back()->with('error', 'مجموع النسب المئوية يجب أن يكون 100% ❌');
        }
    }

    // 2. تحديث البيانات
    foreach ($values as $partnerId => $val) {
        $partner = Partner::find($partnerId);
        if (!$partner) continue;

        if ($type === 'amount') {
            // المعطى مبلغ -> نحسب النسبة
            $amount = $val;
            $percentage = ($amount / $total) * 100;
        } else {
            // المعطى نسبة -> نحسب المبلغ
            $percentage = $val;
            $amount = ($percentage / 100) * $total;
        }

        $partner->update([
            'percentage' => round($percentage, 2),
            'last_capital_snapshot' => round($amount, 2)
        ]);
    }

    return back()->with('success', 'تم تحديث حسابات الشركاء بنجاح بناءً على ' . ($type == 'amount' ? 'المبالغ' : 'النسب') . ' ✅');
}
}
