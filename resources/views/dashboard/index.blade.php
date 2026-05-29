@extends('layouts.app')

@section('content')
    <style>
        /* تنسيقات واجهة لوحة التحكم الرئيسية */
        .dashboard-header {
            text-align: center;
            margin-bottom: 4rem;
            animation: fadeInDown 0.8s ease forwards;
        }

        .dashboard-header h1 {
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--text-main), var(--primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }

        .dashboard-header p {
            color: var(--text-muted);
            font-size: 1.1rem;
        }

        /* شبكة الكروت (Grid) ريسبونسف بالكامل */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 2rem;
            max-width: 1100px;
            margin: 0 auto;
        }

        /* تصميم الكارت (غير تقليدي) */
        .feature-card {
            position: relative;
            background: linear-gradient(145deg, rgba(20, 25, 35, 0.6), rgba(10, 12, 18, 0.4));
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 3rem 2rem;
            text-align: center;
            text-decoration: none;
            color: var(--text-main);
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 1;
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInUp 0.6s ease forwards;
        }

        /* أنيميشن دخول الكروت بالترتيب */
        .feature-card:nth-child(1) {
            animation-delay: 0.1s;
        }

        .feature-card:nth-child(2) {
            animation-delay: 0.2s;
        }

        .feature-card:nth-child(3) {
            animation-delay: 0.3s;
        }

        .feature-card:nth-child(4) {
            animation-delay: 0.4s;
        }

        .feature-card:nth-child(5) {
            animation-delay: 0.5s;
        }

        /* تأثير إضاءة خلفية عند تمرير الماوس */
        .feature-card::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 0;
            height: 0;
            background: radial-gradient(circle, rgba(0, 242, 254, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            transition: width 0.5s ease, height 0.5s ease, opacity 0.5s ease;
            opacity: 0;
            z-index: -1;
        }

        .feature-card:hover {
            transform: translateY(-10px) scale(1.03);
            border-color: rgba(0, 242, 254, 0.4);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4), 0 0 20px rgba(0, 242, 254, 0.1);
        }

        .feature-card:hover::before {
            width: 300%;
            height: 300%;
            opacity: 1;
        }

        /* الأيقونة داخل الكارت */
        .feature-card .icon {
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            filter: drop-shadow(0 5px 15px rgba(0, 0, 0, 0.3));
            transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .feature-card:hover .icon {
            transform: scale(1.2) rotate(-5deg);
        }

        /* نصوص الكارت */
        .feature-card h3 {
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            letter-spacing: 0.5px;
        }

        .feature-card p {
            font-size: 0.9rem;
            color: var(--text-muted);
            line-height: 1.6;
            transition: color 0.3s;
        }

        .feature-card:hover p {
            color: #d1d9e6;
        }

        /* سهم تفاعلي يظهر عند الـ Hover */
        .feature-card .action-arrow {
            position: absolute;
            bottom: 1.5rem;
            opacity: 0;
            transform: translateY(10px);
            color: var(--primary);
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .feature-card:hover .action-arrow {
            opacity: 1;
            transform: translateY(0);
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInDown {
            0% {
                opacity: 0;
                transform: translateY(-20px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ====== أزرار التنقل الجديدة ====== */
        .nav-controls {
            position: fixed;
            top: 2rem;
            right: calc(2rem + 60px);
            /* يوضع بجانب زر المنيو */
            display: flex;
            gap: 10px;
            z-index: 1001;
        }

        .btn-nav {
            background: var(--card-bg);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(10px);
            color: white;
            padding: 0 15px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            text-decoration: none;
            transition: 0.3s;
            font-weight: 500;
        }

        .btn-nav:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--primary);
        }

        .btn-nav svg {
            width: 20px;
            height: 20px;
        }

        /* تحسين السناك بار ليدعم الألوان */
        #snackbar {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%) translateY(100px);
            min-width: 300px;
            padding: 16px 24px;
            border-radius: 16px;
            z-index: 10000;
            font-weight: bold;
            text-align: center;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            opacity: 0;
            display: block !important;
            /* نستخدم الـ Opacity للتحريك */
            pointer-events: none;
        }

        #snackbar.show {
            transform: translateX(-50%) translateY(0);
            opacity: 1;
        }

        #snackbar.success {
            background: linear-gradient(135deg, #00b09b, #96c93d);
            color: #000;
            box-shadow: 0 10px 30px rgba(150, 201, 61, 0.3);
        }

        #snackbar.error {
            background: linear-gradient(135deg, #ff416c, #ff4b2b);
            color: #fff;
            box-shadow: 0 10px 30px rgba(255, 75, 43, 0.3);
        }

        /* ريسبونسيف للموبايل */
        @media (max-width: 768px) {
            .nav-controls {
                right: auto;
                left: 2rem;
                /* في الموبايل انقلهم لليسار لأن المنيو على اليمين */
                top: 2rem;
            }

            .btn-nav span {
                display: none;
                /* إخفاء النص في الموبايل والبقاء على الأيقونة */
            }
        }
    </style>

    <div class="dashboard-header">
        <h1>مرحباً بك في لوحة التحكم</h1>
        <p>اختر القسم الذي تريد إدارته من الخيارات بالأسفل</p>
    </div>

    <div class="features-grid">
        <a href="{{ route('transactions.index') }}" class="feature-card">
            <div class="icon">💰</div>
            <h3>سجل الحركات و رأس المال</h3>
            <p>إدارة بيانات رأس المال، الأرصدة، والميزانية العامة للنظام.</p>
            <div class="action-arrow">دخول ←</div>
        </a>

        <a href="{{ route('expense-types.index') }}" class="feature-card">
            <div class="icon">📂</div>
            <h3>أنواع المصروف</h3>
            <p>إضافة وتصنيف فئات المصروفات المختلفة لسهولة الإدارة.</p>
            <div class="action-arrow">دخول ←</div>
        </a>

        <a href="{{ route('analytics.index') }}" class="feature-card">
            <div class="icon">📊</div>
            <h3>التقارير</h3>
            <p>عرض التحليلات والإحصائيات الشاملة لاتخاذ قرارات أفضل.</p>
            <div class="action-arrow">دخول ←</div>
        </a>
        <a href="{{ route('system-accounts.index') }}" class="feature-card">
            <div class="icon">🏦</div>
            <h3>حسابات النظام والشركاء</h3>
            <p>إدارة حسابات النظام والتعامل مع الشركاء والنسب في رأس المال، متابعة الأرصدة، وحركات التحويل بين الحسابات.</p>
            <div class="action-arrow">دخول ←</div>
        </a>
    </div>
@endsection
