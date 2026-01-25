@extends('layouts.app')

@section('page_title', 'المخزن')

<style>
    :root {
        --prime: #ddcdbc;
        --prime-soft: #e6ddd4;
        --bg: #515831;
        --bg-dark: #3f4526;
        --white: #ffffff;
    }

    /* الخلفية العامة */
    body {
        background: linear-gradient(-45deg, var(--bg), var(--bg-dark), var(--bg));
        background-size: 400% 400%;
        animation: gradientMove 14s ease infinite;
        color: var(--white);
        font-family: system-ui, sans-serif;
    }

    @keyframes gradientMove {
        0% {
            background-position: 0% 50%;
        }

        50% {
            background-position: 100% 50%;
        }

        100% {
            background-position: 0% 50%;
        }
    }

    .container {
        max-width: 1300px;
        margin: auto;
        padding: 30px 20px;
    }

    /* العدادين */
    .stats-row {
        display: flex;
        justify-content: center;
        gap: 20px;
        flex-wrap: wrap;
        margin-bottom: 30px;
    }

    .stats-box {
        background: rgba(221, 205, 188, 0.15);
        backdrop-filter: blur(14px);
        padding: 20px 30px;
        border-radius: 18px;
        text-align: center;
        min-width: 220px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .25);
    }

    .stats-box p:first-child {
        color: var(--prime-soft);
        font-weight: 700;
    }

    .stats-box p:last-child {
        font-size: 28px;
        font-weight: 900;
        color: var(--prime);
    }

/* زر الإضافة الجديد */
#addButton {
    top: 80px; /* المسافة من الأعلى */
    right: 40px; /* المسافة من اليمين */
    width: 120px;
    height: 50px;
    border-radius: 12px; /* مش دايرة كاملة */
    border: none;
    background: linear-gradient(135deg, var(--prime), var(--prime-soft));
    color: var(--bg);
    font-size: 28px;
    font-weight: 800;
    cursor: pointer;
    box-shadow: 0 10px 25px rgba(0, 0, 0, .35);
    transition: .3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

#addButton:hover {
    transform: scale(1.05) translateY(-2px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.45);
}



    /* البحث */
    .search-box {
        text-align: center;
        margin-bottom: 30px;
    }

    .search-box input {
        width: 420px;
        max-width: 100%;
        padding: 14px 20px;
        border-radius: 20px;
        border: none;
        outline: none;
        background: rgba(221, 205, 188, 0.2);
        color: var(--white);
        font-size: 15px;
    }

    .search-box input::placeholder {
        color: var(--prime-soft);
    }

    /* كروت المنتجات */
    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 22px;
    }

    .product-card {
        background: rgba(221, 205, 188, 0.15);
        backdrop-filter: blur(14px);
        border-radius: 20px;
        padding: 18px;
        box-shadow: 0 15px 40px rgba(0, 0, 0, .35);
        cursor: pointer;
        transition: .3s ease;
        border: 1px solid rgba(221, 205, 188, 0.25);
    }

    .product-card:hover {
        transform: translateY(-6px) scale(1.02);
        background: rgba(221, 205, 188, 0.25);
    }

    .product-title {
        font-weight: 900;
        font-size: 17px;
        color: var(--prime);
        margin-bottom: 8px;
    }

    .product-info {
        font-size: 14px;
        color: var(--prime-soft);
        margin: 4px 0;
    }

    .product-badge {
        display: inline-block;
        margin-top: 10px;
        padding: 6px 12px;
        border-radius: 12px;
        background: linear-gradient(135deg, var(--prime), var(--prime-soft));
        color: var(--bg);
        font-weight: 800;
        font-size: 13px;
    }
</style>

@section('content')
    <div class="container">

        {{-- الإشعارات --}}
        @if (session('success'))
            <div class="snackbar success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="snackbar error">{{ session('error') }}</div>
        @endif

        {{-- العدادين + زر الإضافة --}}
        <div class="stats-row">
            <div class="stats-box">
                <p>عدد المنتجات</p>
                <p>{{ $countItems }}</p>
            </div>

            <button id="addButton" data-bs-toggle="modal" data-bs-target="#chooseActionModal">+</button>

            <div class="stats-box">
                <p>عدد الأصناف</p>
                <p>{{ $countProducts }}</p>
            </div>
        </div>

        {{-- البحث --}}
        <div class="search-box">
            <input type="text" id="searchBox" placeholder="🔍 ابحث عن منتج">
        </div>

        {{-- الكروت --}}
        <div class="products-grid" id="productsGrid">
            @foreach ($products as $product)
                <div class="product-card">

                    <div class="product-title">{{ $product->name }}</div>

                    <div class="product-info">💰 السعر: {{ $product->price }}</div>
                    <div class="product-info">📦 التكلفة: {{ $product->cost }}</div>
                    <div class="product-info">🔢 الكمية: {{ $product->quantity }}</div>

                    <span class="product-badge">ID #{{ $product->id }}</span>
                </div>
            @endforeach
        </div>
    </div>

    

    {{-- البحث AJAX --}}
    <script>
        document.getElementById('searchBox').addEventListener('keyup', function() {
            let query = this.value;

            fetch("{{ route('products.search') }}?query=" + query)
                .then(res => res.json())
                .then(data => {
                    let grid = document.getElementById('productsGrid');
                    grid.innerHTML = "";

                    if (!data.length) {
                        grid.innerHTML =
                            `<p style="grid-column:1/-1;text-align:center;color:var(--prime-soft)">لا توجد نتائج</p>`;
                        return;
                    }

                    data.forEach(item => {
                        grid.innerHTML += `
                    <div class="product-card" data-href="/products/${item.id}">
                        <div class="product-title">${item.name}</div>
                        <div class="product-info">💰 السعر: ${item.price}</div>
                        <div class="product-info">📦 التكلفة: ${item.cost}</div>
                        <div class="product-info">🔢 الكمية: ${item.quantity}</div>
                        <span class="product-badge">ID #${item.id}</span>
                    </div>
                `;
                    });

                    document.querySelectorAll('.product-card').forEach(card => {
                        card.addEventListener('click', function() {
                            window.location.href = this.dataset.href;
                        });
                    });
                });
        });
    </script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchProduct');
    const resultsList = document.getElementById('searchResults');
    const form = document.getElementById('addQuantityForm');
    const productIdInput = document.getElementById('product_id');

    searchInput.addEventListener('keyup', function () {
        const query = this.value.trim();

        if (!query) {
            resultsList.innerHTML = '';
            form.style.display = 'none';
            return;
        }

        fetch(`{{ route('products.search') }}?query=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(data => {
                resultsList.innerHTML = '';

                if (!data.length) {
                    resultsList.innerHTML = `<li class="list-group-item text-center text-muted">لا توجد نتائج</li>`;
                    form.style.display = 'none';
                    return;
                }

                data.forEach(product => {
                    const li = document.createElement('li');
                    li.className = 'list-group-item list-group-item-action';
                    li.textContent = `${product.name} | الكمية: ${product.quantity}`;
                    li.style.cursor = 'pointer';
                    li.addEventListener('click', () => {
                        productIdInput.value = product.id;
                        searchInput.value = product.name;
                        resultsList.innerHTML = '';
                        form.style.display = 'block';
                    });
                    resultsList.appendChild(li);
                });
            })
            .catch(err => {
                console.error(err);
                resultsList.innerHTML = `<li class="list-group-item text-center text-danger">حدث خطأ</li>`;
            });
    });
});
</script>

    {{-- المودالات --}}
    @include('products.modals.choose-action')
    @include('products.modals.add-product')
    @include('products.modals.add-quantity')

@endsection
