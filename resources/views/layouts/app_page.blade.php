<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">

    <meta charset="UTF-8">
    <title>

        @yield('title')

    </title>
    @yield('style')
    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        /* مساحة افتراضية أعلى الصفحة (يتم تحديثها ديناميكياً بالـ JS) */
        :root {
            --theme-primary: #d9b2ad;
            --accent-2: #ffe8ee;
            --top-offset: 72px;
            --theme-color: #e5c6c3;
            --theme-color-active: #e5c6c3;
            --nav-text: #333;
            --nav-active-bg: #fff;

        }

        /* نستخدم المتغير لتعويد المحتوى على وجود أزرار ثابتة أعلاه */
        body {
            /* نحفظ المسافة الفارغة أعلى المحتوى لتفادي تداخل العناصر الثابتة */
            padding-top: calc(var(--top-offset) + 12px);
            transition: padding-top .18s ease;

        }

        /* ✅ Navbar */
        .navbar-custom {
            position: fixed;
            top: 0;
            right: 0;
            left: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            padding: 14px 24px;
            backdrop-filter: blur(10px);
            z-index: 1500;
            border-radius: 0 0 16px 16px;
        }

        .navbar-custom a {
            text-decoration: none;
            color: var(--nav-text);
            font-weight: 600;
            padding: 10px 18px;
            border-radius: 8px;
            transition: all 0.25s ease;
        }

        .navbar-custom a:hover {
            background: #e5c6c33a;
            transform: translateY(-2px);
        }

        .navbar-custom a.active {
            background: var(--theme-color-active);
            color: #000;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
            transform: scale(1.05);
        }

        .back-btn {
            position: fixed;
            top: 60px;
            /* نزلناه شويه تحت */
            left: 20px;
            background: #e5c6c3;
            border-radius: 50%;
            padding: 13px;
            cursor: pointer;
            transition: transform 0.2s;
            z-index: 2000;
            border: none;
        }

        /* زرّ الرجوع */
        .back-btn2 {
            position: fixed;
            top: 12px;
            /* فوق شويه */
            left: 20px;
            background: #e5c6c3;
            border: none;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            z-index: 2001;
            /* أعلى من الرئيسية */
        }

        /* تأثير hover */
        .back-btn:hover,
        .back-btn2:hover {
            transform: scale(1.1);
            background: #d9b3b0;
        }

        /* أيقونة السهم */
        .back-btn2 i {
            color: #333;
            font-size: 16px;
        }

        /* زرّات الإجراءات في أعلى اليمين (لو عندك) يجب أن يكون لها هذه الصفة */
        .page-actions {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 2000;
        }

        /* Snackbar: لا نضعه عند top:20px مباشرة، بل نستعمل المتغير بحيث يكون أسفل الأزرار الثابتة */
        .snackbar {
            position: fixed;
            top: calc(var(--top-offset) + 8px);
            /* أسفل الأزرار الثابتة */
            right: 20px;
            background: #333;
            color: #fff;
            padding: 12px 18px;
            border-radius: 10px;
            font-size: 14px;
            z-index: 1990;
            /* أقل من الأزرار الثابتة حتى لا يغطيها */
            opacity: 0;
            transform: translateX(120%);
            transition: opacity 0.4s ease, transform 0.4s ease;
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

        /* لو في أكثر من snackbar نتباعد بينهم */
        .snackbar-stack+.snackbar-stack {
            margin-top: 8px;
        }

        /* حماية على الشاشات الصغيرة: نقّص المسافة لو تحتاج */
        @media (max-width: 520px) {
            :root {
                --top-offset: 64px;
            }

            body {
                padding-top: calc(var(--top-offset) + 8px);
            }

            .snackbar {
                right: 12px;
                left: 12px;
                top: calc(var(--top-offset) + 8px);
            }
        }

        .title {
            text-align: center;
            margin: 8px 0 18px;
            color: var(--theme-primary);
            font-size: 22px;
            font-weight: 800;
            padding: 12px 18px;
            border-radius: 12px;
            background: linear-gradient(180deg, rgba(217, 178, 173, 0.06), rgba(217, 178, 173, 0.02));
            border: 1px solid rgba(217, 178, 173, 0.10);
            box-shadow: 0 8px 22px rgba(217, 178, 173, 0.06);
        }

        @media (max-width:720px) {
            .title {
                font-size: 18px;
                padding: 10px 14px;
            }
        }

        @media (max-width: 768px) {
            .desktop-nav {
                display: none !important;
            }
        }

        /* إظهارها على الديسكتوب */
        @media (min-width: 769px) {
            .desktop-nav {
                display: block;
            }
        }

        .edit-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(90deg, #ffd966cd 0%, #ffb803db 100%);
            color: #2b2b2b;
            padding: 10px 14px;
            border-radius: 12px;
            font-weight: 800;
            box-shadow: 0 8px 22px rgba(255, 183, 3, 0.18), 0 2px 6px rgba(0, 0, 0, 0.06);
            text-decoration: none;
            transform: translateY(0);
            transition: transform .18s cubic-bezier(.2, .9, .3, 1), box-shadow .18s, filter .18s;
            border: 1px solid rgba(0, 0, 0, 0.06);
            position: relative;
            overflow: visible;
        }

        .edit-btn .edit-ico {
            font-size: 18px;
            transform-origin: center;
            display: inline-block;
            transition: transform .24s ease;
        }

        .edit-btn .edit-txt {
            font-size: 14px;
            letter-spacing: .2px;
        }

        .edit-btn:focus {
            outline: none;
            box-shadow: 0 12px 30px rgba(255, 179, 3, 0.18);
        }

        .edit-btn:hover {
            transform: translateY(-6px) scale(1.02);
            box-shadow: 0 18px 40px rgba(255, 170, 3, 0.22);
            filter: saturate(1.05);
        }

        .edit-btn:hover .edit-ico {
            transform: translateY(-2px) rotate(-12deg) scale(1.05);
        }

        /* نبض خفيف حول الزر (pseudo) */
        .edit-btn::after {
            content: '';
            position: absolute;
            left: -6px;
            right: -6px;
            top: -6px;
            bottom: -6px;
            border-radius: 16px;
            z-index: -1;
            opacity: 0;
            transition: opacity .25s, transform .25s;
            background: radial-gradient(closest-side, rgba(255, 190, 60, 0.12), transparent 40%);
            transform: scale(.95);
            pointer-events: none;
        }

        /* نشغل النبضة مرة عند تحميل الصفحة */
        .edit-btn {
            animation: btnPulse 1.1s ease 0s 1;
        }

        .client-snackbar {
            position: fixed;
            top: 24px;
            right: 24px;
            background: #fff;
            color: #333;
            border-radius: 16px;
            padding: 16px 18px;
            width: 320px;
            box-shadow: 0 18px 40px rgba(0, 0, 0, .15);
            z-index: 9999;
            animation: slideIn .35s ease;
        }

        .client-snackbar h4 {
            margin: 0 0 6px;
            font-weight: 800;
            color: #e5c6c3;
        }

        .client-snackbar p {
            margin: 0;
            font-size: 14px;
        }

        .client-snackbar button {
            margin-top: 12px;
            width: 100%;
            padding: 10px;
            border-radius: 12px;
            border: none;
            background: linear-gradient(135deg, #e5c6c3, #d9b2ad);
            font-weight: 800;
            cursor: pointer;
        }

        @keyframes slideIn {
            from {
                transform: translateX(120%);
                opacity: 0
            }

            to {
                transform: translateX(0);
                opacity: 1
            }
        }
    </style>



    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">

</head>

<body>

    <div class="desktop-nav">
        @include('layouts.page_navigation')
    </div>
    @yield('page_title')
    @if (session('shift_required'))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: '⚠️ لم تفتح شيفت بعد',
                    text: 'هل تريد فتح شيفت جديد الآن؟',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'نعم، افتح شيفت',
                    cancelButtonText: 'لاحقًا'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch("{{ route('shift.start') }}", {
                            method: "POST",
                            headers: {
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            }
                        }).then(() => {
                            location.reload();
                        });
                    }
                });
            });
        </script>
    @endif

    {{-- ✅ تنبيهات Toast باستخدام Snackbar --}}
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
    @if (session('warning'))
        <div class="alert alert-warning">
            {{ session('warning') }}
        </div>
    @endif

    {{-- زر الرجوع للرئيسيه --}}
    <form action="{{ route('main.create') }}">
        <button class="back-btn">الرئيسية</button>
    </form>

    <button type="button" class="back-btn2" onclick="history.back()">
        <i class="fas fa-arrow-left"></i>
    </button>

    @yield('content')

    @include('session.modal.start-booking')
    @include('session.modal.active_sub_modal')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://js.pusher.com/8.2/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.3/dist/echo.iife.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            @if (session('client_has_active_sub'))
                console.log("فتح المودال");
                var modalEl = document.getElementById('activeSubModal');
                if (modalEl) {
                    var modal = new bootstrap.Modal(modalEl);
                    modal.show();
                }
            @endif
        });
    </script>
    <script>
        function showSnackbar(message, type = "success") {
            const box = document.createElement('div');
            box.className = `snackbar ${type}`;
            box.textContent = message;
            document.body.appendChild(box);

            setTimeout(() => box.classList.add("show"), 50);

            setTimeout(() => {
                box.classList.remove("show");
                setTimeout(() => box.remove(), 300);
            }, 4000);
        }
    </script>
    <script>
        /* ==========================================================================
                   1. Global State & Configuration
                   ========================================================================== */
        let persons = 1;
        let isSearching = false;
        let buffer = '';
        let scanTimer = null;

        const MIN_LENGTH = 4;
        const MAX_LENGTH = 5;
        const SCAN_TIMEOUT = 2000; // وقت انتظار انتهاء الكتابة بالسكنر

        // تعريف الأصوات مع معالجة الأخطاء
        const safeAudio = (src) => {
            const audio = new Audio(src);
            audio.onerror = () => console.warn('Audio file not found:', src);
            return audio;
        };

        const sounds = {
            start: safeAudio('/sounds/start.mp3'),
            success: safeAudio('/sounds/success.mp3'),
            entry: safeAudio('/sounds/entry.mp3'),
            error: safeAudio('/sounds/error.mp3'),
            cancel: safeAudio('/sounds/cancel.mp3'),
        };

        /* ==========================================================================
           2. Event Listeners (Main Logic)
           ========================================================================== */
        document.addEventListener('DOMContentLoaded', () => {

            // عناصر الـ UI الثابتة في الصفحة
            const hallSelect = document.getElementById('hallSelect');
            const startNowBtn = document.getElementById('startNowBtn');
            const startBookingForm = document.getElementById('startBookingForm');

            // أ. مستمع لوحة المفاتيح (الماسح الضوئي)
            let lastKeyTime = Date.now();

            document.addEventListener('keydown', (e) => {
                if (isSearching || isTypingInField(e)) return;

                // ESC
                if (e.key === 'Escape') {
                    closeClientSnackbar();
                    resetScan();
                    return;
                }

                // ENTER (بعض السكانرات بتبعت Enter)
                if (e.key === 'Enter') {
                    if (buffer.length >= MIN_LENGTH) {
                        isSearching = true;
                        searchClientById(buffer);
                    }
                    resetScan();
                    return;
                }

                // حساب سرعة الكتابة (عشان نعرف scanner ولا لأ)
                const now = Date.now();
                const diff = now - lastKeyTime;
                lastKeyTime = now;

                const isScanner = diff < 30; // سريع جدا = scanner

                // أرقام فقط
                if (!/^[0-9]$/.test(e.key)) {
                    if (buffer.length > 0) resetScan('invalid');
                    return;
                }

                if (buffer.length === 0) {
                    startTimeout();
                    sounds.start.play().catch(() => {});
                }

                buffer += e.key;

                clearTimeout(scanTimer);

                scanTimer = setTimeout(() => {
                    if (buffer.length >= MIN_LENGTH && buffer.length <= MAX_LENGTH) {
                        isSearching = true;
                        searchClientById(buffer);
                    }
                    resetScan();
                }, isScanner ? 100 : 400); // أسرع للـ scanner
            });
            document.addEventListener('paste', (e) => {
                if (isSearching) return;

                const pasted = e.clipboardData.getData('text').trim();

                if (/^\d{4,5}$/.test(pasted)) {
                    isSearching = true;
                    searchClientById(pasted);
                }
            });


            // ب. مستمع لتغيير القاعة لتحديث التقدير المالي
            if (hallSelect) {
                hallSelect.addEventListener('change', fetchEstimate);
            }

            // ج. التحقق من البيانات قبل إرسال فورم الحجز الخاص
            if (startBookingForm) {
                startBookingForm.addEventListener('submit', function(e) {
                    const name = document.getElementById('modal_name').value;
                    if (!name) {
                        e.preventDefault();
                        alert('عفواً، بيانات العميل غير مكتملة.');
                    }
                });
            }
        });

        /* ==========================================================================
           3. API & Data Functions
           ========================================================================== */

        // البحث عن العميل
        async function searchClientById(id) {
            try {
                const res = await fetch(`{{ route('clients.search.id') }}?query=${id}`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();

                if (!data || !data.length) {
                    sounds.error.play();
                    showClientSnackbar(null, '❌ لا يوجد عميل بهذا الكود');
                    return;
                }

                const client = data[0];

                // حالة 1: العميل لديه جلسة مفتوحة (تحويل مباشر)
                if (client.active_session_id) {
                    sounds.entry.play();
                    window.location.href = `{{ url('/sessions') }}/${client.active_session_id}`;
                    return;
                }

                // حالة 2: عميل مسجل (فتح واجهة الاختيار)
                persons = 1; // ريست للعدد
                showClientSnackbar(client);
                sounds.success.play();

            } catch (err) {
                console.error("Search Error:", err);
                sounds.error.play();
            } finally {
                isSearching = false;
            }
        }

        // جلب التقدير المالي وفحص حالة القاعة
        async function fetchEstimate() {
            const hallId = document.getElementById('hallSelect')?.value;
            const banner = document.getElementById('estimateBanner');
            const msg = document.getElementById('estimateMessage');
            const warning = document.getElementById('ongoingWarning');
            const startNowBtn = document.getElementById('startNowBtn');

            if (!hallId) {
                if (startNowBtn) startNowBtn.disabled = true;
                if (banner) banner.style.display = 'none';
                return;
            }

            // إظهار حالة التحميل
            banner.style.display = 'block';
            msg.textContent = 'جارِ فحص القاعة وحساب التكلفة...';
            warning.style.display = 'none';

            try {
                const params = new URLSearchParams({
                    hall_id: hallId,
                    attendees: persons,
                    duration_minutes: 60
                });

                const resp = await fetch("{{ route('bookings.estimate') }}?" + params.toString(), {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                const data = await resp.json();

                if (data.success) {
                    document.getElementById('estimateAmount').textContent =
                        `${data.estimated_formatted} ${data.currency || 'EGP'}`;
                    document.getElementById('estimatePerHour').textContent =
                        `سعر الساعة: ${data.per_hour_formatted || ''}`;
                    msg.textContent = `التقدير (المدة: ساعة)`;

                    // التحقق من الانشغال
                    if (data.ongoing) {
                        warning.style.display = 'block';
                        document.getElementById('ongoingText').textContent = data.message || 'القاعة مشغولة حالياً';
                        startNowBtn.disabled = true;
                    } else {
                        startNowBtn.disabled = false;
                    }
                } else {
                    msg.textContent = 'تنبيه:';
                    warning.style.display = 'block';
                    document.getElementById('ongoingText').textContent = data.error || 'لا يمكن البدء في هذه القاعة';
                    startNowBtn.disabled = true;
                }
            } catch (err) {
                console.error("Fetch Error:", err);
                msg.textContent = 'خطأ في الاتصال بالسيرفر';
            }
        }

        /* ==========================================================================
           4. UI Helper Functions
           ========================================================================== */

        function showClientSnackbar(client, errorMsg = null) {
            persons = 1;

            document.querySelector('.client-snackbar')?.remove();
            const box = document.createElement('div');
            box.className = 'client-snackbar';

            if (!client) {
                box.innerHTML = `<p>${errorMsg}</p>`;
                document.body.appendChild(box);
                setTimeout(() => box.remove(), 2500);
                return;
            }

            const justStartRoute = document.getElementById('route-just-start').value;
            const newSessionRoute = document.getElementById('route-new-session').value;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            box.innerHTML = `
    <div style="display:flex; justify-content:space-between; align-items:center;">
        <h4 style="margin:0;">👤 ${client.name}</h4>
        <button type="button" style="
            margin-right: 20%;
            background: #e5c6c3;
            border: none;
            color: #ff0303;
            font-size: 28px;

            cursor: pointer;
        " onclick="closeClientSnackbar()">×</button>
    </div>
    <div class="persons-counter">
        <button type="button" onclick="updatePersons(-1)">➖</button>
        <span id="snack-persons-display">${persons}</span>
        <button type="button" onclick="updatePersons(1)">➕</button>
    </div>
    <div class="d-flex gap-2 mt-2">
        <form action="{{ route('session.justStart') }}" method="POST" style="flex:1">
            @csrf
            <input type="hidden" name="phone" value="${client.phone}">
            <input type="hidden" name="client_id" value="${client.id}">
            <input type="hidden" name="name" class="persons-sync" value="${client.name}">
            <input type="hidden" name="persons" class="persons-sync" value="${persons}">
            <button type="submit" class="btn-primary-sm">🚀 جلسة عامة</button>
            <button type="button" id="openPrivateBtn" class="btn-private-sm">🔒 جلسة خاصة</button>
        </form>
    </div>
`;
            document.body.appendChild(box);

            // فتح المودال الخاص
            box.querySelector('#openPrivateBtn').addEventListener('click', () => openPrivateModal(client));

            // 🔹 حفظ الجلسة تلقائيًا بعد ظهور Snackbar
            setTimeout(async () => {
                try {
                    const response = await fetch(newSessionRoute, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            client_id: client.id,
                            persons: persons,
                            start_time: new Date().toISOString().slice(0, 19).replace('T', ' ')
                        })
                    });

                    const data = await response.json();
                    if (data.success) console.log('✅ تم تخزين الجلسة تلقائيًا');
                    else console.warn('⚠️ لم يتم تخزين الجلسة:', data.message);

                } catch (err) {
                    console.error('❌ خطأ عند الاتصال بالسيرفر:', err);
                }
            }, 2000);
        }

        // تحديث عدد الأشخاص ومزامنة جميع الحقول والمودالات
        function updatePersons(val) {
            persons = Math.max(1, persons + val);

            // 1. تحديث عرض الـ Snackbar
            const snackDisplay = document.getElementById('snack-persons-display');
            if (snackDisplay) snackDisplay.innerText = persons;

            // 2. تحديث الحقول المخفية في الفورم
            document.querySelectorAll('.persons-sync').forEach(el => el.value = persons);

            // 3. تحديث المودال (إذا كان مفتوحاً أو سيتم فتحه)
            const modalDisplay = document.getElementById('personsDisplayInModal');
            const modalInput = document.getElementById('modal_persons');
            if (modalDisplay) modalDisplay.innerText = persons;
            if (modalInput) modalInput.value = persons;

            // 4. إعادة الحساب تلقائياً إذا كانت القاعة مختارة
            if (document.getElementById('hallSelect')?.value) {
                fetchEstimate();
            }
        }

        function openPrivateModal(client) {
            document.getElementById('modal_name').value = client.name;
            document.getElementById('modal_phone').value = client.phone;
            document.getElementById('modal_persons').value = persons;
            document.getElementById('personsDisplayInModal').innerText = persons;

            const modalEl = document.getElementById('startBookingModal');
            const modal = new bootstrap.Modal(modalEl);
            modal.show();

            // تحديث السعر فور فتح المودال
            fetchEstimate();
        }

        function isTypingInField(e) {
            return ['INPUT', 'TEXTAREA', 'SELECT'].includes(e.target.tagName) || e.target.isContentEditable;
        }

        function resetScan(reason = '') {
            buffer = '';
            clearTimeout(scanTimer);
            if (reason === 'invalid' || reason === 'timeout') {
                sounds.cancel.play().catch(() => {});
            }
        }

        function startTimeout() {
            clearTimeout(scanTimer);
            scanTimer = setTimeout(() => resetScan('timeout'), 5000);
        }

        function closeClientSnackbar() {
            document.querySelector('.client-snackbar')?.remove();
        }
    </script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <input type="hidden" id="route-just-start" value="{{ route('session.justStart') }}">
    <input type="hidden" id="route-new-session" value="{{ route('new-session.store') }}">
</body>

</html>
