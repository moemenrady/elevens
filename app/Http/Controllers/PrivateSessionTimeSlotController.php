<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Hall;
use App\Models\PrivateSessionTimeSlot;
use App\Models\VenuePricing;
use App\Services\PricingService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PrivateSessionTimeSlotController extends Controller
{
    // 1. بدء فترة جديدة (Start Slot)
    public function store(Request $request, Booking $booking)
    {
        $request->validate([
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'attendees_count' => 'required|integer|min:1',
            'total_amount' => 'required|numeric',
        ]);

        $slot = $booking->timeSlots()->create([
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'attendees_count' => $request->attendees_count,
            'total_amount' => $request->total_amount,
        ]);

        // تحديث الإجمالي الفعلي للحجز
        $booking->update([
            'real_total' => $booking->timeSlots()->sum('total_amount')
        ]);

        return response()->json(['message' => 'تم حفظ الفترة بنجاح', 'slot' => $slot]);
    }

    // 2. تعديل بيانات الجلسة وهي شغالة (مثلا تعديل عدد الأفراد)
    public function update(Request $request, $id)
    {
        $slot = PrivateSessionTimeSlot::findOrFail($id);

        $request->validate([
            'start_time' => 'required',
            'end_time' => 'required',
            'attendees_count' => 'required|integer',
            'total_amount' => 'required|numeric'
        ]);

        $slot->update([
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'attendees_count' => $request->attendees_count,
            'total_amount' => $request->total_amount,
        ]);

        // تحديث إجمالي الحجز الأساسي
        $booking = $slot->booking;
        $booking->update(['real_total' => $booking->timeSlots()->sum('total_amount')]);

        return response()->json(['success' => true, 'message' => 'تم التحديث بنجاح']);
    }
    // 3. إنهاء الجلسة وحساب التكلفة (End Slot)
    public function endSession(Request $request, PrivateSessionTimeSlot $timeSlot)
    {
        if ($timeSlot->end_time) {
            return response()->json(['message' => 'هذه الجلسة منتهية بالفعل'], 400);
        }

        $endTime = $request->end_time ? Carbon::parse($request->end_time) : Carbon::now();
        $startTime = $timeSlot->start_time;

        // حساب المدة بالدقائق
        $durationMinutes = $startTime->diffInMinutes($endTime);
        $durationHours = $durationMinutes / 60;

        $booking = $timeSlot->booking;
        $cost = 0;

        // منطق حساب التكلفة (كمثال بناءً على الأعمدة الموجودة في Booking)
        if ($booking->base_hour_price > 0) {
            $baseCost = $durationHours * $booking->base_hour_price;
            $extraAttendees = max(0, $timeSlot->attendees_count - $booking->min_capacity_snapshot);
            $extraCost = $durationHours * $extraAttendees * $booking->extra_person_hour_price;

            $cost = $baseCost + $extraCost;
        }

        // تحديث الجلسة
        $timeSlot->update([
            'end_time' => $endTime,
            'total_amount' => round($cost, 2),
        ]);

        // (اختياري) تحديث الإجمالي الفعلي للحجز الأساسي
        $booking->update([
            'real_total' => $booking->timeSlots()->sum('total_amount')
        ]);

        return response()->json(['message' => 'تم إنهاء الجلسة وحساب التكلفة', 'slot' => $timeSlot]);
    }

    // 4. إلغاء/حذف الجلسة (Destroy)
    public function destroy(PrivateSessionTimeSlot $timeSlot)
    {
        $booking = $timeSlot->booking;
        $timeSlot->delete();

        // تحديث الإجمالي بعد الحذف
        $booking->update([
            'real_total' => $booking->timeSlots()->sum('total_amount')
        ]);

        return response()->json(['message' => 'تم إلغاء الجلسة بنجاح']);
    }

    public function estimate_time_slot(Request $request, PricingService $pricing)
    {
        $data = $request->validate([
            'hall_id' => ['required', 'exists:halls,id'],
            'attendees' => ['required', 'integer', 'min:1'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
        ]);

        try {
            // تأكد أن هناك سعر أساسي متوفر
            if (VenuePricing::get()->isNotEmpty()) {
                $base = DB::table('venue_pricing')->value('base_hour_price');
                if ($base === null) {
                    return response()->json(['error' => 'لا يوجد سعر أساسي مضبوط حالياً.'], 422);
                }
            } else {
                return response()->json(['error' => 'لا يوجد سعر اساسي للساعة حتى الآن.'], 422);
            }

            $hall = Hall::findOrFail($data['hall_id']);
            $minCapacity = $hall->min_capacity ?? 1;

            // استخدم الService لحساب التقدير
            $estimated = $pricing->setBase((float) $base)->total(
                (int) $data['attendees'],
                (int) $minCapacity,
                (int) $data['duration_minutes']
            );

            // حساب سعر الساعة للعرض (اختياري)
            $perHour = $pricing->readPerHour((int) $data['attendees'], (int) $minCapacity, (int) $base, (int) ($base / 2));

            // رجّع JSON (مع تنسيق رقمى قابل للعرض)
            return response()->json([
                'success' => true,
                'estimated' => round($estimated, 2),
                'estimated_formatted' => number_format($estimated, 2, '.', ','),
                'per_hour' => round($perHour, 2),
                'per_hour_formatted' => number_format($perHour, 2, '.', ','),
                'currency' => 'جنيه'
            ]);
        } catch (\Throwable $e) {
            Log::error('Estimate pricing failed: ' . $e->getMessage(), ['request' => $request->all()]);
            return response()->json(['error' => 'حدث خطأ أثناء حساب السعر.'], 500);
        }
    }
}
