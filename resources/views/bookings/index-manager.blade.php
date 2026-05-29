@extends('layouts.app_page')

@section('title', 'إدارة الحجوزات')

@section('content')
    <div class="page-container">
    @section('page_title')
    <h1 class="title">📅 إدارة الحجوزات</h1> @endsection
    @if (session('success'))
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                showSnackbar("{{ session('success') }}", "success");
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                showSnackbar("{{ session('error') }}", "error");
            });
        </script>
    @endif
    <div class="page-actions">
        <a href="{{ route('bookings.create') }}" class="add-booking-btn" aria-label="اضافة حجز">اضافة حجز</a>
    </div>
    <div class="filters-box card shadow-sm mb-4 p-3">
        <div class="row g-3 align-items-end">

            {{-- البحث --}}
            <div class="col-12 col-md-6">
                <label class="form-label">🔍 بحث</label>
                <input type="text" id="searchBox" class="form-control form-control-lg"
                    placeholder="اسم الحجز / العميل / الهاتف / ID">
            </div>

            {{-- الفلاتر بالحالة --}}
            <div class="col-12 col-md-6">
                <label class="form-label d-block">⚡ الحالة</label>
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-outline-secondary status-filter-btn" data-status="scheduled"
                        data-active="false">ليس الآن</button>
                    <button type="button" class="btn btn-outline-warning status-filter-btn" data-status="due"
                        data-active="false">لم يبدأ</button>
                    <button type="button" class="btn btn-outline-info status-filter-btn" data-status="in_progress"
                        data-active="false">جاري</button>
                </div>

            </div>

        </div>
    </div>
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">

        <button id="toggleSelection" class="btn btn-outline-dark rounded-pill px-4 fw-bold">
            🗂️ وضع التحديد
        </button>

        <div id="selectionActions" class="d-none align-items-center gap-3 bg-light px-4 py-2 rounded-pill shadow-sm">

            <span id="selectedCount" class="fw-bold text-danger">0 محدد</span>

            <button id="deleteSelected" class="btn btn-danger btn-sm rounded-pill px-3">
                🗑️ إلغاء المحدد
            </button>

            <button id="cancelSelection" class="btn btn-secondary btn-sm rounded-pill px-3">
                إلغاء
            </button>

        </div>

    </div>
    {{-- الكروت --}}
    <div class="bookings-list" id="bookingsList">
        <p class="text-center p-3">⏳ جاري التحميل...</p>
    </div>ِ
</div>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ar.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let searchBox = document.getElementById("searchBox");
        let statusButtons = document.querySelectorAll(".status-filter-btn");

        let bookingsList = document.getElementById("bookingsList");
        let fromDate = null,
            toDate = null;

        // route للـ show
        let showRoute = @json(route('bookings.show', ':id'));



        function formatDateTime(dateStr) {
            if (!dateStr) return "-";
            let d = new Date(dateStr);
            return d.toLocaleString("ar-EG", {
                year: "numeric",
                month: "short",
                day: "2-digit",
                hour: "2-digit",
                minute: "2-digit",
            });
        }

        function fetchBookings() {
            let q = searchBox.value || '';

            // نجيب الأزرار المفعل عليها فقط
            let activeStatuses = Array.from(statusButtons)
                .filter(btn => btn.dataset.active === "true")
                .map(btn => btn.dataset.status);

            let params = new URLSearchParams({
                q
            });
            if (fromDate) params.append("from", fromDate);
            if (toDate) params.append("to", toDate);
            activeStatuses.forEach(s => params.append("statuses[]", s));

            fetch("{{ route('bookings.ajaxSearchManager') }}?" + params.toString())
                .then(res => res.json())
                .then(data => {
                    bookingsList.innerHTML = "";
                    let bookingsArray = Array.isArray(data) ? data : Object.values(data);

                    if (!bookingsArray.length) {
                        bookingsList.innerHTML = `<p class="no-results">❌ لا توجد نتائج</p>`;
                        return;
                    }

                    bookingsArray.forEach(b => {
                        let actionBtns = "";
                        if (b.status === "scheduled" || b.status === "due") {
                            actionBtns =
                                `<a href="/bookings/${b.id}/edit" class="btn btn-sm btn-outline-primary">✏️ تعديل</a>`;
                        }

                        const weekdayNames = ['الحد', 'الاتنين', 'التلات', 'الأربع', 'الخميس',
                            'الجمعة', 'السبت'
                        ];
                        const startDate = new Date(b.start_at);
                        const weekdayLabel = weekdayNames[startDate.getDay()];

                        // لو جاري، نستخدم real_start_at بدل start_at
                        let displayTime = b.status === "in_progress" && b.real_start_at ?
                            formatTime12(b.real_start_at) :
                            formatTime12(b.start_at);

                        // لون كونتينر الوقت
                        let timeContainerColor = b.status === "in_progress" ? "info" : "primary";

                        bookingsList.innerHTML += `
<div class="booking-card"
     data-id="${b.id}"
     style="cursor:pointer; position: relative;">
    <!-- وقت البدء في كونتينر -->
    <div class="booking-time bg-${timeContainerColor}">
        ${displayTime}
    </div>

    <div class="info">
        <h3>👤 <strong>${b.client_name || '-'}</strong></h3>
        <p>🏛️ ${b.hall_name || '-'}</p>
        <p class="weekday">📅 ${weekdayLabel} / ${formatDayMonth(b.start_at)}</p>
    </div>

    <div class="meta">
        <span class="badge bg-${statusColor(b.status)}">${statusLabel(b.status)}</span>
        <p class="mt-2">💰 ${parseFloat(b.estimated_total).toFixed(2)}</p>
        <div class="actions mt-2">${actionBtns}</div>
    </div>
</div>`;
                    });
                })
                .catch(err => {
                    bookingsList.innerHTML = `<p class="no-results">❌ حدث خطأ أثناء جلب البيانات</p>`;
                    console.error(err);
                });
        }
        let selectionMode = false;
        let selectedIds = new Set();

        let toggleBtn = document.getElementById("toggleSelection");
        let cancelBtn = document.getElementById("cancelSelection");
        let deleteBtn = document.getElementById("deleteSelected");
        let selectionBar = document.getElementById("selectionActions");
        let selectedCount = document.getElementById("selectedCount");

        function updateSelectedUI() {
            selectedCount.textContent = selectedIds.size + " محدد";
        }

        function enableSelectionMode() {
            selectionMode = true;
            document.body.classList.add("selection-mode");
            selectionBar.classList.remove("d-none");
        }

        function disableSelectionMode() {
            selectionMode = false;
            selectedIds.clear();
            document.body.classList.remove("selection-mode");
            selectionBar.classList.add("d-none");

            document.querySelectorAll(".booking-card").forEach(card => {
                card.classList.remove("selected");
            });

            updateSelectedUI();
        }

        toggleBtn.addEventListener("click", enableSelectionMode);
        cancelBtn.addEventListener("click", disableSelectionMode);
        deleteBtn.addEventListener("click", async function() {

            if (selectedIds.size === 0) {
                showSnackbar("اختر حجوزات أولاً", "error");
                return;
            }

            if (!confirm("هل أنت متأكد من إلغاء الحجوزات المحددة؟")) return;

            try {
                let response = await fetch("{{ route('bookings.bulkCancel') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                            .content
                    },
                    body: JSON.stringify({
                        ids: Array.from(selectedIds)
                    })
                });

                let data = await response.json();

                if (response.ok && data.success) {
                    showSnackbar("✅ تم إلغاء الحجوزات بنجاح", "success");
                    disableSelectionMode();
                    fetchBookings(); // إعادة تحميل النتائج
                } else {
                    showSnackbar("❌ حدث خطأ أثناء الإلغاء", "error");
                }

            } catch (err) {
                showSnackbar("❌ فشل الاتصال بالسيرفر", "error");
            }
        });
        document.addEventListener("click", function(e) {

            let card = e.target.closest(".booking-card");
            if (!card) return;

            let id = card.dataset.id;

            // ===== وضع التحديد =====
            if (selectionMode) {
                e.preventDefault();
                e.stopPropagation();

                if (card.classList.contains("selected")) {
                    selectedIds.delete(id);
                    card.classList.remove("selected");
                } else {
                    selectedIds.add(id);
                    card.classList.add("selected");
                }

                updateSelectedUI();
                return;
            }

            // ===== الوضع العادي =====
            window.location.href = showRoute.replace(':id', id);
        });

        function formatDayMonth(dateStr) {
            if (!dateStr) return "-";
            let d = new Date(dateStr);
            return `${d.getDate()} / ${d.getMonth() + 1}`; // اليوم / الشهر
        }

        function statusColor(status) {
            switch (status) {
                case "scheduled":
                    return "secondary";
                case "due":
                    return "warning";
                case "in_progress":
                    return "info";
                case "finished":
                    return "success";
                case "cancelled":
                    return "danger";
                default:
                    return "dark";
            }
        }

        function formatTime12(dateStr) {
            if (!dateStr) return "-";
            let d = new Date(dateStr);
            let hours = d.getHours();
            let minutes = d.getMinutes().toString().padStart(2, '0');
            let ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12;
            hours = hours ? hours : 12; // ساعة 0 تصبح 12
            return `${hours}:${minutes} ${ampm}`;
        }

        function statusLabel(status) {
            switch (status) {
                case "scheduled":
                    return "ليس الآن";
                case "due":
                    return "لم يبدأ";
                case "in_progress":
                    return "جاري";
                case "finished":
                    return "منتهي";
                case "cancelled":
                    return "ملغي";
                default:
                    return "غير معروف";
            }
        }

        searchBox.addEventListener("keyup", fetchBookings);
        statusButtons.forEach(btn => {
            btn.addEventListener("click", () => {
                // toggle حالة الزر
                if (btn.dataset.active === "true") {
                    btn.dataset.active = "false";
                    btn.classList.remove("active");
                } else {
                    btn.dataset.active = "true";
                    btn.classList.add("active");
                }
                fetchBookings(); // جلب النتائج بعد التغيير
            });
        });

        fetchBookings(); // تحميل أولي
    });
</script>



@endsection

@section('style')
<style>
    .booking-card.selected {
        background: rgba(255, 203, 154, 0.3);
        border-left: 5px solid #ffa94d;
    }

    .selection-mode .booking-card {
        cursor: pointer;
    }

    .page-container {
        max-width: 1000px;
        margin: auto;
        padding: 20px;
    }


    /* ===== Snackbar ===== */
    .snackbar {
        position: fixed;
        top: 20px;
        right: 20px;
        background: #333;
        color: #fff;
        padding: 12px 18px;
        border-radius: 10px;
        font-size: 14px;
        z-index: 9999;
        opacity: 0;
        transform: translateX(120%);
        transition: opacity 0.4s ease, transform 0.4s ease;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .snackbar.show {
        opacity: 1;
        transform: translateX(0);
    }

    .snackbar.success {
        background: #28a745;
    }

    .snackbar.error {
        background: #dc3545;
    }

    .snackbar i {
        font-size: 16px;
    }

    .page-actions {
        position: fixed;
        top: 16px;
        right: 16px;
        /* ثابت في أقصى اليمين */
        z-index: 1000;
    }

    .status-filter-btn.active {
        color: #fff !important;
        background-color: currentColor;
        /* سيأخذ لون الزر الأساسي */
        border-color: currentColor;
    }

    .add-booking-btn {
        position: relative;
        display: inline-block;
        padding: 12px 18px;
        background: var(--btn-bg);
        color: var(--btn-text);
        font-weight: 800;
        /* Bold */
        font-size: 15px;
        border: 1px solid var(--btn-border);
        border-radius: 14px;
        text-decoration: none;
        letter-spacing: .2px;
        box-shadow: 0 6px 14px rgba(0, 0, 0, .12), inset 0 -2px 0 rgba(0, 0, 0, .05);
        transition: transform .25s ease, box-shadow .25s ease, background-color .25s ease, border-color .25s ease;
        overflow: hidden;
        /* لإخفاء الوميض أثناء الحركة */
        -webkit-tap-highlight-color: transparent;
    }

    /* لمعان عصري يمر على الزر */
    .add-booking-btn::before {
        content: "";
        position: absolute;
        inset: -120% -30%;
        background: linear-gradient(120deg, transparent 35%, rgba(255, 255, 255, .65) 50%, transparent 65%);
        transform: translateX(-100%);
        transition: transform .6s ease;
        pointer-events: none;
    }

    .add-booking-btn:hover {
        background-color: var(--btn-bg-hover);
        transform: translateY(-2px) scale(1.02);
        box-shadow: 0 10px 22px rgba(0, 0, 0, .16), inset 0 -2px 0 rgba(0, 0, 0, .05);
        border-color: #e9c94e;
    }

    .booking-time {
        position: absolute;
        top: 15px;
        left: 15px;
        background-color: #007bff;
        /* أزرق */
        color: #fff;
        padding: 5px 10px;
        border-radius: 6px;
        font-weight: bold;
        font-size: 14px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        z-index: 10;
    }

    .booking-card .weekday {
        font-weight: 600;
        color: #6c757d;
        margin: 4px 0;
    }

    .add-booking-btn:hover::before {
        transform: translateX(100%);
    }

    /* تأثير ضغط خفيف */
    .add-booking-btn:active {
        transform: translateY(0) scale(0.99);
        box-shadow: 0 6px 14px rgba(0, 0, 0, .12), inset 0 -2px 0 rgba(0, 0, 0, .08);
    }

    /* وضيح لليوزرز باستخدام الكيبورد */
    .add-booking-btn:focus {
        outline: none;
        box-shadow:
            0 0 0 3px rgba(255, 228, 131, .6),
            0 10px 22px rgba(0, 0, 0, .16),
            inset 0 -2px 0 rgba(0, 0, 0, .05);
    }

    /* احترام إعدادات تقليل الحركة */
    @media (prefers-reduced-motion: reduce) {

        .add-booking-btn,
        .add-booking-btn::before {
            transition: none;
        }

        .add-booking-btn:hover {
            transform: none;
        }
    }

    .bookings-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .booking-card {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        background: #fff;
        border-radius: 10px;
        padding: 15px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        border-top: 4px solid #d9b2ad;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .booking-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    }

    .booking-card .info {
        flex: 2;
        font-size: 14px;
    }

    .booking-card .info h3 {
        margin: 0 0 5px;
        font-size: 16px;
        color: #333;
    }

    .booking-card .meta {
        flex: 1;
        text-align: right;
        font-size: 13px;
    }

    .booking-card .actions a {
        display: block;
        margin-bottom: 4px;
    }

    .no-results {
        text-align: center;
        color: #888;
    }
</style>
@endsection
