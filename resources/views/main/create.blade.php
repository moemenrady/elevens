@extends('layouts.app')

@section('page_title', 'الصفحة الرئيسية')

@section('content')

    <main class="container py-5">

        <h1 class="main-title">الصفحة الرئيسية</h1>

        <div class="main-grid">

            <div class="card">
                <form action="{{ route('sale_proccess.create') }}" method="GET">
                    <button type="submit" class="card-btn">
                        <h2>بيع منتجات</h2>
                    </button>
                </form>
            </div>

            <div class="card">
                <form action="{{ route('invoice.index') }}" method="GET">
                    <button type="submit" class="card-btn">
                        <h2>المبيعات</h2>
                    </button>
                </form>
            </div>

            @if (Auth::user()->role === 'admin')
                <div class="card">
                    <form action="{{ route('products.index') }}" method="GET">
                        <button type="submit" class="card-btn">
                            <h2>المخزن</h2>
                        </button>
                    </form>
                </div>
            @endif

        </div>

    </main>

    <style>
        /* العنوان */
        .main-title {
            text-align: center;
            font-size: 26px;
            font-weight: 900;
            color: var(--prime);
            margin-bottom: 30px;
        }

        /* الجريد */
        .main-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 22px;
            max-width: 600px;
            margin: auto;
        }

        /* الكارت */
        .card {
            background: rgba(221, 205, 188, 0.18);
            backdrop-filter: blur(14px);
            border: 1px solid rgba(221, 205, 188, 0.35);
            border-radius: 20px;
            padding: 26px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, .35);

            opacity: 0;
            transform: translateX(-40px);
            animation: fadeIn .6s ease forwards;

            transition: transform .15s ease, box-shadow .15s ease;
        }

        /* زرار الكارت */
        .card-btn {
            all: unset;
            width: 100%;
            height: 100%;
            display: block;
            cursor: pointer;
            color: var(--white);
        }

        .card h2 {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: .5px;
            color: var(--prime);
        }

        /* أنيميشن الدخول */
        @keyframes fadeIn {
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* حركة ذكية بالماوس */
        .card:hover {
            transform: translateY(-6px) scale(1.03);
            box-shadow: 0 30px 70px rgba(0, 0, 0, .45);
        }
    </style>

    <script>
        const cards = document.querySelectorAll(".card");

        /* stagger animation */
        cards.forEach((card, i) => {
            card.style.animationDelay = `${i * 0.12}s`;
        });

        /* smart hover repulsion */
        cards.forEach(card => {
            card.addEventListener("mousemove", e => {
                const rect = card.getBoundingClientRect();
                const x = (e.clientX - rect.left) / rect.width - 0.5;
                const y = (e.clientY - rect.top) / rect.height - 0.5;

                const force = 20;
                const moveX = x * force;
                const moveY = y * force * -1;

                card.style.transform = `translate(${moveX}px, ${moveY}px) scale(1.03)`;
            });

            card.addEventListener("mouseleave", () => {
                card.style.transform = "translate(0,0) scale(1)";
            });
        });
    </script>

@endsection
