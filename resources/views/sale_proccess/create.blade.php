@extends('layouts.app_page')

@section('title', 'بدء عملية بيع')


@section('content')

    <div class="sale-container">

        <!-- Search -->
        <input type="text" id="searchInput" placeholder="ابحث بالاسم أو ID أو اسكنر الباركود..." class="search-input"
            autofocus autocomplete="off">


        <div class="products-list" id="productsList"></div>



    </div>

    <!-- Cart -->
    <div class="cart-panel" id="cartPanel">
        <h5>🛒 المشتريات</h5>
        <div id="cartItems"></div>

        <button id="submitOrder" class="btn btn-success w-100 mt-2">
            تأكيد البيع
        </button>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            let cart = [];
            let timer = null;

            const searchInput = $("#searchInput");
            const productsList = $("#productsList");
            const cartItems = $("#cartItems");
            const submitOrder = $("#submitOrder");

            /* =========================
                تحميل المنتجات أول ما الصفحة تفتح
            ========================= */
            loadProducts("");

            /* =========================
                أي كتابة تروح تلقائي على البحث
            ========================= */
            $(document).on("keydown", function() {
                if (document.activeElement !== searchInput[0]) {
                    searchInput.focus();
                }
            });

            /* =========================
                البحث من السيرفر
            ========================= */
            searchInput.on("input", function() {
                const query = $(this).val();

                if (timer) clearTimeout(timer);

                timer = setTimeout(() => {
                    loadProducts(query);
                }, 180);
            });

            /* =========================
                جلب المنتجات من السيرفر
            ========================= */
            function loadProducts(query) {
                $.ajax({
                    url: "{{ route('products.search') }}",
                    method: "GET",
                    data: {
                        query
                    },
                    success: function(data) {
                        renderProducts(data);
                    }
                });
            }

            /* =========================
                رسم كروت المنتجات (شكل موحد)
            ========================= */
            function renderProducts(data) {
                let html = "";

                if (!data.length) {
                    html = `<div class="text-muted text-center p-2">لا توجد نتائج</div>`;
                } else {
                    data.forEach(p => {
                        html += `
                    <button class="product-item" 
                        data-id="${p.id}" 
                        data-name="${p.name}"
                        data-stock="${p.quantity}">
                        
                        ${p.name}
                        <small>#${p.id}</small>
                        <small class="stock-text">المتاح: ${p.quantity}</small>
                    </button>
                `;
                    });
                }

                productsList.html(html);
            }

            /* =========================
                إضافة منتج للسلة
            ========================= */
            $(document).on("click", ".product-item", function() {
                const id = $(this).data("id");
                const stock = $(this).data("stock");
                const name = $(this).data("name");


                if (stock <= 0) {
                    alert("❌ المنتج غير متوفر في المخزن");
                    return;
                }

                const existing = cart.find(p => p.id == id);

                if (existing) {
                    existing.qty++;
                } else {
                    cart.push({
                        id,
                        name,
                        qty: 1
                    });
                }

                updateCartUI();
            });

            /* =========================
                تحديث واجهة السلة
            ========================= */
            function updateCartUI() {
                cartItems.html("");

                cart.forEach((item, index) => {
                    cartItems.append(`
                <div class="cart-item">
                    <span>${item.name}</span>
                    <div class="qty-controls">
                        <button onclick="changeQty(${index}, -1)">➖</button>
                        <strong>${item.qty}</strong>
                        <button onclick="changeQty(${index}, 1)">➕</button>
                        <button onclick="removeItem(${index})">❌</button>
                    </div>
                </div>
            `);
                });
            }

            /* =========================
                التحكم في الكميات
            ========================= */
            window.changeQty = function(index, change) {
                cart[index].qty += change;
                if (cart[index].qty <= 0) cart.splice(index, 1);
                updateCartUI();
            };

            window.removeItem = function(index) {
                cart.splice(index, 1);
                updateCartUI();
            };

            /* =========================
                تأكيد البيع
            ========================= */
            submitOrder.on("click", function() {
                if (!cart.length) {
                    alert("اختر منتجات أولاً");
                    return;
                }

                const items = encodeURIComponent(JSON.stringify(cart));
                window.location.href = "{{ route('invoice.create') }}" + "?items=" + items;
            });

        });
    </script>


@endsection

@section('style')
    <style>
        .search-input {
            width: 100%;
            padding: 14px 16px;
            border-radius: 14px;
            border: 1px solid var(--prime-soft);
            background: var(--white);
            font-size: 15px;
            outline: none;
            transition: border .2s ease;
        }

        .search-input:focus {
            border-color: var(--prime);
        }

        .products-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 10px;
        }

        .product-item {
            background: var(--prime);
            border: 1px solid var(--prime-soft);
            border-radius: 10px;
            padding: 8px 10px;
            /* أصغر */
            font-size: 14px;
            /* أصغر */
            font-weight: 700;
            /* أوضح */
            text-align: right;
            cursor: pointer;
            transition: all .2s ease;
            display: flex;
            flex-direction: column;
            gap: 2px;
            color: #1f1f1f;
            /* أغمق وأوضح */
            box-shadow: 0 3px 8px rgba(0, 0, 0, .08);
        }

        .product-item small {
            color: #2b2b2b;
            /* أغمق من قبل */
            font-size: 11px;
            font-weight: 500;
        }



        .product-item:hover {
            background: #e0d2bf;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, .1);
        }

        .cart-panel {
            position: static;
            margin-top: 20px;
            background: #fff;
            width: 100%;
            max-width: 400px;
            padding: 15px;
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, .15);
        }


        .cart-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .qty-controls button {
            border: none;
            background: #eee;
            padding: 3px 7px;
            border-radius: 6px;
            margin: 0 2px;
        }

        .sale-container {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        @media (min-width: 992px) {
            .sale-container {
                grid-template-columns: 2fr 1fr;
                align-items: start;
            }

            .cart-panel {
                position: sticky;
                top: 20px;
            }
        }

        .stock-badge {
            background: #2e7d32;
            color: #fff;
            font-size: 11px;
            padding: 3px 8px;
            border-radius: 8px;
            width: fit-content;
        }

        .stock-badge.out {
            background: #b71c1c;
        }
    </style>
@endsection
