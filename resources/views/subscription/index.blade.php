@extends('layouts.app')
@section('title', 'إدارة الاشتراكات')

<style>
    body {
        font-family: "Tahoma", sans-serif;
        background: linear-gradient(to bottom, #fff, #fce9d9);
        margin: 0;
        padding: 0;
        color: #333;
    }

    /* الزرار نفسه */
    #addButton {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        /* يشيل الخط الأزرق */
        background: #ffcb9a;
        font-size: 48px;
        font-weight: bold;
        color: #000;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        transition: 0.3s;
        margin: 20px auto;
        /* يخليه وسط الصفحة */
    }

    #addButton:hover {
        background: #ffa94d;
        transform: scale(1.05);
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 30px 20px;
    }

    /* مربع البحث */
    .search-box {
        margin: 20px auto;
        text-align: center;
    }

    .search-box input {
        padding: 14px 20px;
        width: 450px;
        max-width: 100%;
        border-radius: 25px;
        border: 1px solid #ddd;
        font-size: 15px;
        outline: none;
        transition: 0.2s;
        background: #fff;
    }

    .search-box input:focus {
        border-color: #ffcb9a;
        box-shadow: 0 0 6px rgba(255, 170, 80, 0.5);
    }

    /* الفلاتر */
    .filters-box {
        display: flex;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 25px;
    }

    .form-check-lg .form-check-input {
        width: 1.5em;
        height: 1.5em;
        margin-top: .2em;
    }

    .form-check-lg .form-check-label {
        font-size: 1.05em;
        margin-right: .3em;
    }

    /* الجدول */
    table {
        width: 100%;
        border-collapse: collapse;
        background: transparent;
        border-radius: 12px;
        overflow: hidden;
        margin-top: 20px;
    }

    thead {
        background: rgba(255, 224, 178, 0.8);
    }

    thead th {
        padding: 16px 20px;
        text-align: center;
        font-size: 15px;
        font-weight: bold;
        color: #444;
    }

    tbody tr {
        border-bottom: 1px solid #eee;
        text-align: center;
        transition: background 0.2s;
    }

    tbody tr:hover {
        background: rgba(255, 247, 240, 0.7);
    }

    tbody td {
        padding: 14px 18px;
        font-size: 15px;
        color: #333;
    }

    /* الموبايل */
    @media (max-width: 768px) {
        .filters-box {
            flex-direction: column;
            align-items: flex-start;
        }

        .search-box input {
            width: 100%;
        }

        table,
        thead,
        tbody,
        th,
        td,
        tr {
            display: block;
            width: 100%;
        }

        thead {
            display: none;
        }

        tbody tr {
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            background: rgba(255, 255, 255, 0.9);
        }

        tbody td {
            text-align: right;
            padding: 8px 10px;
            position: relative;
            font-size: 14px;
        }

        tbody td::before {
            content: attr(data-label);
            position: absolute;
            left: 10px;
            font-weight: bold;
            color: #666;
        }
    }

    /* وضع التحديد */
    .selection-active .select-col {
        display: table-cell !important;
    }

    .subscription-row.selected {
        background: rgba(255, 203, 154, 0.3);
        border-left: 5px solid #ffa94d;
    }

    .subscription-row {
        transition: all 0.2s ease;
    }

    #snackbar {
        visibility: hidden;
        min-width: 250px;
        background-color: #333;
        color: #fff;
        text-align: center;
        border-radius: 30px;
        padding: 14px 20px;
        position: fixed;
        z-index: 9999;
        right: 30px;
        bottom: 30px;
        font-size: 14px;
        opacity: 0;
        transition: all 0.3s ease;
    }

    #snackbar.show {
        visibility: visible;
        opacity: 1;
    }

    #snackbar.success {
        background-color: #28a745;
    }

    #snackbar.error {
        background-color: #dc3545;
    }
</style>

@section('content')
    <div class="container">
        {{-- زرار الإضافة --}}
        <a href="{{ route('subscriptions.create') }}" id="addButton">+</a>
        {{-- البحث --}}
        <div class="search-box">
            <input type="text" id="searchBox" placeholder="🔍 بحث (اسم العميل / الهاتف / ID)">
        </div>
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">

            <button id="toggleSelection" class="btn btn-outline-dark rounded-pill px-4 fw-bold">
                🗂️ وضع التحديد
            </button>

            <div id="selectionActions" class="d-none align-items-center gap-3 bg-light px-4 py-2 rounded-pill shadow-sm">
                <span id="selectedCount" class="fw-bold text-danger">0 محدد</span>

                <button id="deleteSelected" class="btn btn-danger btn-sm rounded-pill px-3">
                    🗑️ حذف المحدد
                </button>

                <button id="cancelSelection" class="btn btn-secondary btn-sm rounded-pill px-3">
                    إلغاء
                </button>
            </div>

        </div>

        {{-- الفلاتر --}}
        <div class="filters-box">
            {{-- الحالة --}}
            <div>
                <label class="form-label d-block mb-2">⚡ الحالة</label>
                <div class="d-flex gap-3">
                    <div class="form-check form-check-lg">
                        <input class="form-check-input status-filter" type="checkbox" value="1" id="statusActive">
                        <label class="form-check-label" for="statusActive">فعال</label>
                    </div>
                    <div class="form-check form-check-lg">
                        <input class="form-check-input status-filter" type="checkbox" value="0" id="statusEnded">
                        <label class="form-check-label" for="statusEnded">منتهي</label>
                    </div>
                </div>
            </div>

            {{-- الخطط --}}
            <div>
                <label class="form-label d-block mb-2">📦 الخطط</label>
                <div class="d-flex flex-wrap gap-3">
                    @foreach ($plans as $plan)
                        <div class="form-check form-check-lg">
                            <input class="form-check-input plan-filter" type="checkbox" value="{{ $plan->id }}"
                                id="plan{{ $plan->id }}">
                            <label class="form-check-label" for="plan{{ $plan->id }}">{{ $plan->name }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th class="select-col d-none"></th>
                    <th>#</th>
                    <th>العميل</th>
                    <th>الهاتف</th>
                    <th>الخطة</th>
                    <th>من</th>
                    <th>إلى</th>
                    <th>المتبقي</th>
                    <th>الحالة</th>
                </tr>
            </thead>
            <tbody id="subsTable">
                <tr>
                    <td colspan="9" class="text-center p-3">⏳ جاري التحميل...</td>
                </tr>
            </tbody>
        </table>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // عناصر البحث والتصفية
            let searchBox = document.getElementById("searchBox");
            let statusFilters = document.querySelectorAll(".status-filter");
            let planFilters = document.querySelectorAll(".plan-filter");
            let tableBody = document.getElementById("subsTable");

            // المودال
            let renewModal = new bootstrap.Modal(document.getElementById("renewModal"));
            let renewForm = document.getElementById("renewForm");
            let renewClient = document.getElementById("renewClient");
            let renewPlan = document.getElementById("renewPlan");
            let renewRoute = "{{ route('subscriptions.renew', ['subscription' => ':id']) }}";

            // الراوت الخاص بالعرض

            let showRoute = @json(route('subscriptions.show', ['id' => ':id']));

            // فورمات التاريخ
            function formatDateTime(dateStr) {
                let d = new Date(dateStr);
                let options = {
                    year: "numeric",
                    month: "short",
                    day: "2-digit",
                };
                return d.toLocaleString("en-US", options);
            }

            // الفetch
            function fetchSubs() {
                let q = searchBox.value;
                let statuses = Array.from(statusFilters).filter(c => c.checked).map(c => c.value);
                let plans = Array.from(planFilters).filter(c => c.checked).map(c => c.value);

                let params = new URLSearchParams({
                    q
                });
                statuses.forEach(s => params.append("statuses[]", s));
                plans.forEach(p => params.append("plans[]", p));

                fetch("{{ route('subscriptions.ajaxSearch') }}?" + params.toString())
                    .then(res => res.json())
                    .then(data => {
                        tableBody.innerHTML = "";
                        if (data.length === 0) {
                            tableBody.innerHTML =
                                `<tr><td colspan="9" class="text-center p-3">❌ لا توجد نتائج</td></tr>`;
                        } else {
                            data.forEach(s => {
                                tableBody.innerHTML += `
                                <tr class="subscription-row" data-id="${s.id}">
                                    <td class="select-col d-none">
                                        <input type="checkbox" class="row-check form-check-input">
                                    </td>
                                    <td>${s.id}</td>
                                    <td>${s.client_name}</td>
                                    <td>${s.client_phone}</td>
                                    <td>${s.plan_name}</td>
                                    <td>${formatDateTime(s.start_date) ?? '-'}</td>
                                    <td>${formatDateTime(s.end_date) ?? '-'}</td>
                                    <td>${s.remaining_visits}</td>
                                    <td>
                                        ${s.is_active === "فعال"
                                            ? `<span class="badge bg-success">فعال</span>`
                                            : `<span class="badge bg-secondary">منتهي</span>`}
                                    </td>
                                </tr>`;
                            });

                            attachRowClick();
                        }
                    });
            }

            function showSnackbar(message, type = "success") {
                let snackbar = document.getElementById("snackbar");

                snackbar.textContent = message;
                snackbar.className = "show " + type;

                setTimeout(() => {
                    snackbar.className = snackbar.className.replace("show", "");
                }, 3000);
            }

            // ربط الكلاينت show
            function attachRowClick() {

                document.querySelectorAll(".subscription-row").forEach(row => {

                    row.addEventListener("click", function(e) {

                        let id = this.dataset.id;

                        // ====== وضع التحديد ======
                        if (selectionMode) {

                            e.preventDefault();
                            e.stopPropagation();

                            let checkbox = this.querySelector(".row-check");

                            // Toggle
                            let isSelected = this.classList.contains("selected");

                            if (isSelected) {
                                selectedIds.delete(id);
                                this.classList.remove("selected");
                                checkbox.checked = false;
                            } else {
                                selectedIds.add(id);
                                this.classList.add("selected");
                                checkbox.checked = true;
                            }

                            updateSelectedUI();
                            return;
                        }

                        // ====== الوضع العادي ======
                        window.location.href = showRoute.replace(':id', id);

                    });

                });
            }


            // تشغيل البحث أول مرة
            searchBox.addEventListener("keyup", fetchSubs);
            statusFilters.forEach(cb => cb.addEventListener("change", fetchSubs));
            planFilters.forEach(cb => cb.addEventListener("change", fetchSubs));

            fetchSubs();




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
                document.querySelector("table").classList.add("selection-active");
                selectionBar.classList.remove("d-none");
            }

            function disableSelectionMode() {
                selectionMode = false;
                selectedIds.clear();
                document.querySelector("table").classList.remove("selection-active");
                selectionBar.classList.add("d-none");

                document.querySelectorAll(".subscription-row").forEach(row => {
                    row.classList.remove("selected");
                    let checkbox = row.querySelector(".row-check");
                    if (checkbox) checkbox.checked = false;
                });

                updateSelectedUI();
            }

            toggleBtn.addEventListener("click", enableSelectionMode);
            cancelBtn.addEventListener("click", disableSelectionMode);
            deleteBtn.addEventListener("click", async function() {

                if (selectedIds.size === 0) {
                    showSnackbar("اختر مشتركين أولاً", "error");
                    return;
                }

                if (!confirm("هل أنت متأكد من حذف المحددين؟")) return;

                let successCount = 0;
                let failCount = 0;

                for (let id of selectedIds) {
                    try {
                        let url = "{{ route('subscriptions.destroy', ':id') }}".replace(':id', id);
                        let response = await fetch(url, {
                            method: "DELETE",
                            headers: {
                                "X-CSRF-TOKEN": document.querySelector(
                                    'meta[name="csrf-token"]').content,
                                "Accept": "application/json"
                            }
                        });


                        let data = await response.json();

                        if (response.ok && data.status) {
                            document.querySelector(`tr[data-id="${id}"]`)?.remove();
                            successCount++;
                        } else {
                            failCount++;
                        }

                    } catch (error) {
                        failCount++;
                    }
                }

                disableSelectionMode();

                if (successCount > 0 && failCount === 0) {
                    showSnackbar("✅ تم حذف المحددين بنجاح", "success");
                } else if (successCount > 0 && failCount > 0) {
                    showSnackbar("⚠ تم حذف بعض العناصر والبعض فشل", "error");
                } else {
                    showSnackbar("❌ فشل الحذف", "error");
                }

            });



        });
    </script>


    <!-- ✅ Modal تجديد الاشتراك -->
    <div class="modal fade animate__animated animate__fadeInDown" id="renewModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rounded-4 shadow">

                <!-- الهيدر -->
                <div class="modal-header bg-success text-white rounded-top-4">
                    <h5 class="modal-title">🔄 تجديد الاشتراك</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- الفورم -->
                <form id="renewForm" method="POST" class="p-4">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">👤 العميل</label>
                            <input type="text" id="renewClient" class="form-control form-control-lg" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">📦 الخطة الحالية</label>
                            <input type="text" id="renewPlan" class="form-control form-control-lg" readonly>
                        </div>

                        <div class="col-12">
                            <label for="plan_id" class="form-label">🔄 اختيار خطة أخرى (اختياري)</label>
                            <select name="plan_id" id="plan_id" class="form-select form-select-lg">
                                <option value="">-- نفس الخطة --</option>
                                @foreach ($plans as $plan)
                                    <option value="{{ $plan->id }}">{{ $plan->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- الأزرار -->
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-success btn-lg px-5 fw-bold">✅ إتمام التجديد</button>
                        <button type="button" class="btn btn-outline-secondary btn-lg px-4 ms-2"
                            data-bs-dismiss="modal">إلغاء</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <div id="snackbar"></div>

@endsection
