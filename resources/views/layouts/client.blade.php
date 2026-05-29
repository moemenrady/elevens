<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>11's Menu - Experience</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --color-primary: #2A4444;
            --color-dark: #202020;
            --color-brown: #5A341F;
            --color-bg: #EAE6DE;
            --color-sage: #4C5A3A;
            --color-accent: #C9A27A;
        }

        body {
            font-family: 'Alexandria', sans-serif;
            background-color: var(--color-bg);
            color: var(--color-dark);
            overflow-x: hidden;
            -webkit-tap-highlight-color: transparent;
        }

        /* تحسين أداء الأنيميشن باستخدام كرت الشاشة Hardware Acceleration */
        .smooth-transition {
            transition: all 0.4s cubic-bezier(0.25, 1, 0.5, 1);
            will-change: transform, opacity;
        }

        /* أنيميشن ظهور عناصر المنيو بشكل تدريجي مريح جداً للعين */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(24px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-item {
            opacity: 0;
            animation: fadeInUp 0.5s cubic-bezier(0.25, 1, 0.5, 1) forwards;
        }

        /* التحكم في حركة وأبعاد زر العودة الثابت */
        .back-btn-active {
            transform: scale(1) translateX(0);
            opacity: 1;
            pointer-events: auto;
        }

        .back-btn-hidden {
            transform: scale(0.8) translateX(20px);
            opacity: 0;
            pointer-events: none;
        }

        /* تأثير النبض اللطيف لزر إتمام الفاتورة لمنحه طابع الفخامة */
        @keyframes softPulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.03);
            }
        }

        .pulse-premium {
            animation: softPulse 2s infinite ease-in-out;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="antialiased selection:bg-[#C9A27A] selection:text-white">

    <header
        class="sticky top-0 z-50 bg-[#EAE6DE]/90 backdrop-blur-md px-4 py-4 flex items-center justify-between border-b border-[#2A4444]/10">
        <button id="backBtn" onclick="navigateBack()"
            class="back-btn-hidden smooth-transition flex items-center gap-2 px-4 py-2 rounded-full bg-[#2A4444] text-[#EAE6DE] shadow-lg active:scale-95 group cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 transform group-hover:translate-x-1 transition-transform" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-sm font-semibold">رجوع</span>
        </button>

        <div class="text-center flex-1 pointer-events-none">
            <h1 class="text-2xl font-bold tracking-wider text-[#2A4444]">ELEVEN'S</h1>
            <p class="text-[10px] text-[#5A341F] tracking-widest font-light -mt-1">Made for Quiet Moments</p>
        </div>

        <div class="w-20 div-spacer"></div>
    </header>

    <main class="max-w-4xl mx-auto px-4 py-6 pb-32">

        <section id="view-categories" class="smooth-transition">
            <div class="mb-6">
                <h2 class="text-lg font-bold text-[#202020]">مرحباً بك في 11's</h2>
                <p class="text-xs text-[#4C5A3A]">اختر فئتك المفضلة واستمتع بلحظتك الهادئة</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach ($categories as $category)
                    <div onclick="showCategory('{{ $category->id }}', '{{ $category->name }}')"
                        style="background-color: {{ $category->bg_color ?? '#2A4444' }}; color: {{ $category->text_color ?? '#EAE6DE' }};"
                        class="group relative overflow-hidden p-6 rounded-2xl h-40 flex flex-col justify-between cursor-pointer shadow-md hover:shadow-xl smooth-transition active:scale-98">

                        <div
                            class="absolute -right-4 -bottom-4 opacity-10 font-bold text-5xl tracking-tighter uppercase group-hover:scale-110 smooth-transition pointer-events-none">
                            {{ $category->slug }}
                        </div>

                        @if ($category->badge)
                            <span
                                style="background-color: {{ $category->accent_color ?? '#C9A27A' }}; text-color: {{ $category->bg_color ?? '#202020' }};"
                                class="text-[10px] font-bold px-2 py-1 rounded-md self-start shadow-sm text-black">
                                {{ $category->badge }}
                            </span>
                        @else
                            <div class="w-2 h-2 rounded-full"
                                style="background-color: {{ $category->accent_color ?? '#C9A27A' }}"></div>
                        @endif

                        <h3 class="text-lg font-bold tracking-wide">{{ $category->name }}</h3>
                    </div>
                @endforeach
            </div>
        </section>

        <section id="view-items" class="hidden smooth-transition opacity-0">
            <div class="mb-6">
                <h2 id="category-title" class="text-xl font-bold text-[#2A4444]">اسم الفئة</h2>
                <p class="text-xs text-[#5A341F]">كل رشفة تحكي قصة مختلفة...</p>
            </div>

            <div id="items-container" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            </div>
        </section>

        <section id="view-checkout" class="hidden smooth-transition opacity-0">
            <div class="mb-6 text-center">
                <h2 class="text-xl font-bold text-[#2A4444]">مراجعة طلبك الفاخر</h2>
                <p class="text-xs text-[#5A341F]">الرجاء مراجعة الطلبات قبل إرسال الفاتورة</p>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#2A4444]/10">
                <div id="checkout-items-list" class="divide-y divide-gray-100">
                </div>

                <div class="pt-4 mt-4 border-t border-dashed border-gray-200 space-y-2">
                    <div class="flex justify-between text-sm text-[#4C5A3A]">
                        <span>إجمالي المنتجات</span>
                        <span id="summary-subtotal">0 EGP</span>
                    </div>
                    <div class="flex justify-between text-sm text-[#4C5A3A]">
                        <span>خدمة وضريبة القيمة المضافة</span>
                        <span>مشمولة</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold text-[#202020] pt-2">
                        <span>الحساب الإجمالي</span>
                        <span id="summary-total" class="text-[#5A341F]">0 EGP</span>
                    </div>
                </div>
            </div>

            <button onclick="finalizeOrder()"
                class="w-full mt-8 bg-[#2A4444] text-[#EAE6DE] py-4 rounded-xl font-bold text-center shadow-lg hover:bg-[#2A4444]/90 active:scale-98 smooth-transition pulse-premium cursor-pointer">
                إرسال الطلب للمباشر وإصدار الفاتورة
            </button>
        </section>

    </main>

    <div id="cart-bar"
        class="fixed bottom-6 left-4 right-4 max-w-xl mx-auto bg-[#202020]/95 backdrop-blur-md text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center justify-between transform translate-y-32 opacity-0 smooth-transition z-40 border border-white/10">
        <div class="flex items-center gap-3">
            <div class="relative bg-[#C9A27A] p-2.5 rounded-xl text-[#202020]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <span id="cart-count"
                    class="absolute -top-2 -right-2 bg-[#5A341F] text-white text-xs w-5 h-5 rounded-full flex items-center justify-center font-bold">0</span>
            </div>
            <div>
                <p class="text-xs text-gray-400">إجمالي طلباتك</p>
                <p id="cart-total" class="text-base font-bold text-[#C9A27A]">0 EGP</p>
            </div>
        </div>

        <button onclick="showCheckout()"
            class="bg-[#C9A27A] text-[#202020] px-5 py-2.5 rounded-xl font-bold text-sm hover:bg-[#C9A27A]/90 active:scale-95 smooth-transition cursor-pointer">
            استعراض الفاتورة
        </button>
    </div>
    <script>
        let kioskLocked = true;
        // منع الرجوع
        history.pushState(null, null, location.href);
        window.onpopstate = function() {
            history.pushState(null, null, location.href);
        };

        // منع refresh / shortcuts
        document.addEventListener('keydown', function(e) {
            if (
                e.key === "F5" ||
                (e.ctrlKey && e.key === "r") ||
                (e.ctrlKey && e.shiftKey && e.key === "I") ||
                (e.ctrlKey && e.key === "u")
            ) {
                e.preventDefault();
            }
        });

        // منع right click
        document.addEventListener('contextmenu', e => e.preventDefault());
    </script>
    <script>
        // حقن كامل بيانات الـ Eloquent القادمة من الـ Controller في الـ JavaScript فوراً لضمان تجربة فوريّة بدون Loading ثانية واحدة
        const databaseCategories = @json($categories);

        let currentView = 'categories';
        let cart = {};

        // محرك الصوت المطور التفاعلي الفوري (Web Audio API)
        function playClickSound() {
            try {
                const audioCtx = new(window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioCtx.createOscillator();
                const gainNode = audioCtx.createGain();

                oscillator.type = 'sine';
                oscillator.frequency.setValueAtTime(580, audioCtx.currentTime);
                oscillator.frequency.exponentialRampToValueAtTime(1000, audioCtx.currentTime + 0.05);

                gainNode.gain.setValueAtTime(0.06, audioCtx.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.05);

                oscillator.connect(gainNode);
                gainNode.connect(audioCtx.destination);

                oscillator.start();
                oscillator.stop(audioCtx.currentTime + 0.05);
            } catch (e) {
                console.log("Audio feedback pending interaction");
            }
        }

        // رصد الضغطات لإصدار تأثير الصوت فورياً للأزرار التفاعلية
        document.addEventListener('click', (e) => {
            if (e.target.closest('button') || e.target.closest('[onclick]')) {
                playClickSound();
            }
        });

        // تشغيل الرصد الذكي للأنيميشن وحالة زر العودة الشفاف والثابت
        function updateBackButton() {
            const backBtn = document.getElementById('backBtn');
            if (currentView === 'categories') {
                backBtn.classList.remove('back-btn-active');
                backBtn.classList.add('back-btn-hidden');
            } else {
                backBtn.classList.remove('back-btn-hidden');
                backBtn.classList.add('back-btn-active');
            }
        }

        // محرك الانتقالات الانسيابي (Hardware accelerated Views Router)
        function switchView(targetViewId) {
            const views = ['view-categories', 'view-items', 'view-checkout'];
            currentView = targetViewId.replace('view-', '');

            views.forEach(viewId => {
                const el = document.getElementById(viewId);
                if (viewId === targetViewId) {
                    el.classList.remove('hidden');
                    setTimeout(() => {
                        el.classList.remove('opacity-0', 'translate-y-4');
                    }, 50);
                } else {
                    el.classList.add('opacity-0', 'translate-y-4');
                    el.classList.add('hidden');
                }
            });
            updateBackButton();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // دالة زر العودة (Back Logic)
        function navigateBack() {
            if (currentView === 'checkout') {
                switchView('view-items');
            } else if (currentView === 'items') {
                switchView('view-categories');
            }
        }

        // عرض منتجات الفئة المضغوطة ديناميكياً من الـ JSON المخزن
        function showCategory(categoryId, categoryName) {
            const container = document.getElementById('items-container');
            const titleEl = document.getElementById('category-title');

            titleEl.textContent = categoryName;
            container.innerHTML = '';

            // البحث عن الفئة والمنتجات الخاصة بها داخل كائن الـ Eloquent الممرر
            const selectedCategory = databaseCategories.find(cat => cat.id == categoryId);

            if (!selectedCategory || !selectedCategory.products || selectedCategory.products.length === 0) {
                container.innerHTML =
                    `<p class="text-sm text-gray-500 col-span-2 text-center py-8">لا توجد منتجات متوفرة حالياً في هذه الفئة.</p>`;
                switchView('view-items');
                return;
            }

            // حقن المنتجات المتاحة بتأثير Staggered Animation تدريجي مريح ومبهر للعين
            selectedCategory.products.forEach((product, index) => {
                const count = cart[product.id] ? cart[product.id].qty : 0;

                // استخدام الهوية البصرية وصور المنتجات إذا وجدت
                const imgHTML = product.image ?
                    `<img src="/storage/${product.image}" class="w-16 h-16 rounded-xl object-cover ml-3 shadow-sm" alt="${product.name}">` :
                    '';

                const itemHTML = `
                    <div class="fade-in-item bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex justify-between items-center smooth-transition hover:border-[#C9A27A]" style="animation-delay: ${index * 0.05}s">
                        <div class="flex items-center flex-1 pl-2">
                            ${imgHTML}
                            <div>
                                <h4 class="font-bold text-base text-[#202020]">${product.name}</h4>
                                <p class="text-xs text-[#4C5A3A]/70 font-light mt-0.5">${product.description ?? ''}</p>
                                <span class="inline-block mt-1 font-bold text-sm text-[#5A341F]">${parseFloat(product.price)} EGP</span>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-2 bg-[#EAE6DE] p-1.5 rounded-xl">
                            <button onclick="updateQuantity(${product.id}, -1, ${product.price}, '${product.name}')" class="w-8 h-8 rounded-lg bg-white text-[#202020] flex items-center justify-center font-bold shadow-sm active:scale-90 smooth-transition cursor-pointer">-</button>
                            <span id="qty-${product.id}" class="w-6 text-center font-bold text-sm text-[#202020]">${count}</span>
                            <button onclick="updateQuantity(${product.id}, 1, ${product.price}, '${product.name}')" class="w-8 h-8 rounded-lg bg-[#2A4444] text-[#EAE6DE] flex items-center justify-center font-bold shadow-sm active:scale-90 smooth-transition cursor-pointer">+</button>
                        </div>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', itemHTML);
            });

            switchView('view-items');
        }

        // تحديث محتويات وكميات السلة
        function updateQuantity(id, change, price, name) {
            if (!cart[id]) {
                cart[id] = {
                    name: name,
                    price: parseFloat(price),
                    qty: 0
                };
            }

            cart[id].qty += change;

            if (cart[id].qty <= 0) {
                delete cart[id];
                if (document.getElementById(`qty-${id}`)) document.getElementById(`qty-${id}`).textContent = 0;
            } else {
                if (document.getElementById(`qty-${id}`)) document.getElementById(`qty-${id}`).textContent = cart[id].qty;
            }

            updateCartBar();
        }

        // تحديث البار السفلي العائم وحساب الإجمالي بلمح البصر
        function updateCartBar() {
            let totalItems = 0;
            let totalPrice = 0;

            for (let id in cart) {
                totalItems += cart[id].qty;
                totalPrice += (cart[id].price * cart[id].qty);
            }

            const cartBar = document.getElementById('cart-bar');
            if (totalItems > 0) {
                document.getElementById('cart-count').textContent = totalItems;
                document.getElementById('cart-total').textContent = `${totalPrice} EGP`;

                cartBar.classList.remove('translate-y-32', 'opacity-0');
                cartBar.classList.add('translate-y-0', 'opacity-100');
            } else {
                cartBar.classList.remove('translate-y-0', 'opacity-100');
                cartBar.classList.add('translate-y-32', 'opacity-0');
            }
        }

        // إعداد ونشر شاشة الفاتورة الختامية للعميل
        function showCheckout() {
            const listContainer = document.getElementById('checkout-items-list');
            listContainer.innerHTML = '';
            let subtotal = 0;

            for (let id in cart) {
                const item = cart[id];
                const itemTotal = item.price * item.qty;
                subtotal += itemTotal;

                const checkoutItemHTML = `
                    <div class="flex justify-between items-center py-3">
                        <div>
                            <h5 class="font-bold text-sm text-[#202020]">${item.name}</h5>
                            <p class="text-xs text-gray-400">الكمية: ${item.qty} × ${item.price} EGP</p>
                        </div>
                        <span class="font-bold text-sm text-[#2A4444]">${itemTotal} EGP</span>
                    </div>
                `;
                listContainer.insertAdjacentHTML('beforeend', checkoutItemHTML);
            }

            document.getElementById('summary-subtotal').textContent = `${subtotal} EGP`;
            document.getElementById('summary-total').textContent = `${subtotal} EGP`;

            switchView('view-checkout');
        }

        // إرسال الفاتورة والطلب الفعلي إلى الباك إند عبر Ajax Request صامت وسريع جداً
        function finalizeOrder() {
            if (Object.keys(cart).length === 0) return;

            // تجهيز البيانات لإرسالها لملف الـ Controller للحفظ في الـ Database
            const orderPayload = {
                items: cart,
                _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            };

            // يمكنك ربطها بـ Route تخزين الطلبات الفعلي لديك هنا:
            // fetch('/api/orders/store' أو '/orders')

            alert('تم تأكيد طلبك الفاخر وإرساله لنظام الكافيه بنجاح! طاقم 11\'s يتمنى لك وقتاً ممتعاً.');

            // إعادة تصفير السلة للعميل والعودة لشاشة الترحيب الرئيسية
            cart = {};
            updateCartBar();
            switchView('view-categories');
        }
    </script>
</body>

</html>
