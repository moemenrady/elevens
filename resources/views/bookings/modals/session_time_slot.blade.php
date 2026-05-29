<style>
    #sessionTimeSlot .modal-dialog {
        max-width: 900px;
    }

    #sessionTimeSlot .modal-content {
        border-radius: 16px;
        border: none;
        box-shadow: 0 10px 35px rgba(0, 0, 0, 0.15);
        animation: modalFade .25s ease;
    }

    @keyframes modalFade {
        from {
            transform: translateY(-25px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    #sessionTimeSlot .modal-header {
        background: #4f46e5;
        color: white;
        border-top-left-radius: 16px;
        border-top-right-radius: 16px;
    }

    #sessionTimeSlot .modal-title {
        font-weight: 600;
    }

    #sessionTimeSlot table {
        border-radius: 10px;
        overflow: hidden;
    }

    #sessionTimeSlot table thead {
        background: #f3f4f6;
    }

    #sessionTimeSlot table th {
        font-weight: 600;
        font-size: 14px;
    }

    #sessionTimeSlot table td {
        vertical-align: middle;
    }

    #sessionTimeSlot table tbody tr:hover {
        background: #f9fafb;
    }

    #sessionTimeSlot .price {
        font-weight: 700;
        color: #16a34a;
    }

    #sessionTimeSlot .btn-add-slot {
        border-radius: 10px;
        padding: 10px;
        font-weight: 600;
    }

    #sessionTimeSlot .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    }

    #sessionTimeSlot .card h6 {
        font-weight: 600;
        color: #4f46e5;
    }

    #sessionTimeSlot input {
        border-radius: 8px;
    }

    #sessionTimeSlot .result-box {
        background: #ecfdf5;
        border-radius: 10px;
        padding: 20px;
    }
</style>

<div class="modal fade" id="sessionTimeSlot" tabindex="-1" aria-labelledby="sessionTimeSlotLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sessionTimeSlotLabel">
                    تفاصيل الفترات المحسوبة للحجز
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                <div class="table-responsive mb-4">
                    <table class="table table-hover text-center align-middle">
                        <thead>
                            <tr>
                                <th>من</th>
                                <th>إلى</th>
                                <th>المدة</th>
                                <th>الأفراد</th>
                                <th>الإجمالي</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($booking->timeSlots as $slot)
                                <tr id="slot-row-{{ $slot->id }}">
                                    <td>{{ $slot->start_time->format('h:i A') }}</td>
                                    <td>{{ $slot->end_time ? $slot->end_time->format('h:i A') : 'جاري...' }}</td>
                                    <td>{{ $slot->end_time ? $slot->start_time->diffInMinutes($slot->end_time) . ' دقيقة' : '-' }}
                                    </td>
                                    <td>{{ $slot->attendees_count }}</td>
                                    <td class="price">{{ $slot->total_amount }} جنيه</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-info edit-slot-btn"
                                                data-id="{{ $slot->id }}"
                                                data-start="{{ $slot->start_time->format('Y-m-d\TH:i') }}"
                                                data-end="{{ $slot->end_time ? $slot->end_time->format('Y-m-d\TH:i') : '' }}"
                                                data-attendees="{{ $slot->attendees_count }}">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger delete-slot-btn"
                                                data-id="{{ $slot->id }}">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-muted">لم يتم تقسيم أي فترات حتى الآن.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($hasRemainingTime)
                    <button type="button" class="btn btn-primary w-100 mb-3 btn-add-slot" id="btnToggleNewSlot">
                        + إضافة فترة للوقت المتبقي
                    </button>
                @else
                    <div class="alert alert-info text-center">
                        لا يوجد وقت كافي (أقل من نصف ساعة) لإضافة فترة جديدة.
                    </div>
                @endif

                <!-- الفورم لإضافة / تعديل فترة -->
                <div id="newSlotFormContainer" style="display:none" class="card p-3">
                    <input type="hidden" id="editingSlotId" value="">
                    <h6 class="mb-3">حساب فترة جديدة</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="mb-1">من</label>
                            <input type="datetime-local" id="slotStartTime" class="form-control"
                                value="{{ \Carbon\Carbon::parse($lastSlotEndTime)->format('Y-m-d\TH:i') }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="mb-1">إلى</label>
                            <input type="datetime-local" id="slotEndTime" class="form-control"
                                value="{{ now()->format('Y-m-d\TH:i') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="mb-1">عدد الأفراد</label>
                            <input type="number" id="slotAttendees" class="form-control"
                                value="{{ $booking->attendees }}" min="1">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <button type="button" class="btn btn-dark w-100" id="btnCalculateSlot">
                                احسب التكلفة (Ajax)
                            </button>
                        </div>
                    </div>

                    <div id="slotEstimationResult" class="result-box text-center mt-4" style="display:none">
                        <h4 class="mb-2">التكلفة: <span id="slotEstimatedPrice">0</span> جنيه</h4>
                        <p class="text-muted">المدة: <span id="slotEstimatedDuration">0</span> دقيقة</p>
                        <button type="button" class="btn btn-success px-4" id="btnSaveNewSlot">
                            حفظ الفترة
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
