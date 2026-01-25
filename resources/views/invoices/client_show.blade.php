

@extends('layouts.app_page')
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">

@section('title', 'عرض الفاتورة')

@section('content')
<div class="invoice-wrapper">
    <div class="invoice-card">

        <!-- Header -->
        <div class="invoice-header">
            <h2>فاتورة رقم #{{ $invoice->invoice_number }}</h2>
            <span class="invoice-date">{{ $invoice->created_at->format('d/m/Y') }}</span>
        </div>

        <!-- Client Info -->
        @if (!in_array($invoiceType, ['product']))
        <div class="invoice-section">
            <div class="glass-box">
                <p><strong>العميل:</strong> {{ $invoice->client->name ?? 'غير معروف' }} ({{ $invoice->client->id ?? '-' }})</p>
            </div>
        </div>
        @endif

        <!-- Subscriptions -->
        @if (in_array($invoiceType, ['subscription', 'mixed']))
        <div class="invoice-section">
            <h3>📦 الاشتراكات</h3>
            @foreach ($groupedItems['subscription'] as $item)
                <div class="glass-box">
                    <p>الخطة: {{ $item->name }}</p>
                    <p>السعر: {{ $item->price }} ج</p>
                </div>
            @endforeach
        </div>
        @endif

        <!-- Bookings -->
        @if (in_array($invoiceType, ['booking', 'mixed']))
        <div class="invoice-section">
            <h3>🏢 الحجز</h3>

            <div class="glass-box">
                <p>القاعة: {{ $bookingData->hall->name }}</p>

                @if ($bookingData->real_start_at)
                    <p>بداية: {{ \Carbon\Carbon::parse($bookingData->real_start_at)->format('Y-m-d h:i A') }}</p>
                @endif

                @if ($bookingData->real_end_at)
                    <p>نهاية: {{ \Carbon\Carbon::parse($bookingData->real_end_at)->format('Y-m-d h:i A') }}</p>
                @endif

                <p>سعر الساعة: {{ $hourlyRate }} ج</p>
                <p>الإجمالي: {{ $groupedItems['booking']->first()->total ?? 0 }} ج</p>
            </div>
        </div>
        @endif

        <!-- Purchases -->
        @if ($purchaseItems->isNotEmpty())
        <div class="invoice-section">
            <h3>🛒 المنتجات</h3>

            <div class="purchase-table">
                <div class="purchase-head">
                    <span>المنتج</span>
                    <span>الكمية</span>
                    <span>السعر</span>
                    <span>الإجمالي</span>
                </div>

                @foreach ($purchaseItems as $item)
                <div class="purchase-row">
                    <span>{{ $item->name }}</span>
                    <span>{{ $item->qty }}</span>
                    <span>{{ number_format($item->price, 2) }} ج</span>
                    <span>{{ number_format($item->total, 2) }} ج</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Total -->
        <div class="invoice-total">
            <span>الإجمالي الكلي</span>
            <strong>{{ $totalAmount }} ج</strong>
        </div>

        <p class="invoice-footer">سعدنا بخدمتك 🤍 Rivo</p>

    </div>
</div>
@endsection
@section('style')
<style>
:root {
    --prime: #ddcdbc;
    --prime-soft: #e6ddd4;
    --bg: #515831;
    --bg-dark: #3f4526;
    --white: #ffffff;
}

/* Background */
body {
    background: linear-gradient(-45deg, var(--bg), var(--bg-dark), var(--bg));
    background-size: 400% 400%;
    animation: gradientMove 14s ease infinite;
    color: var(--white);
    font-family: system-ui, sans-serif;
}

@keyframes gradientMove {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

/* Layout */
.invoice-wrapper {
    max-width: 900px;
    margin: 40px auto;
    padding: 20px;
}

.invoice-card {
    background: rgba(221, 205, 188, 0.15);
    backdrop-filter: blur(14px);
    border-radius: 24px;
    padding: 30px;
    box-shadow: 0 20px 50px rgba(0,0,0,.35);
    animation: fadeInUp .6s ease;
}

/* Header */
.invoice-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid rgba(255,255,255,.2);
    padding-bottom: 12px;
    margin-bottom: 20px;
}

.invoice-header h2 {
    color: var(--prime);
    font-size: 22px;
}

.invoice-date {
    background: linear-gradient(135deg, var(--prime), var(--prime-soft));
    color: var(--bg);
    padding: 6px 14px;
    border-radius: 20px;
    font-weight: 800;
}

/* Sections */
.invoice-section {
    margin-bottom: 25px;
}

.invoice-section h3 {
    color: var(--prime);
    margin-bottom: 10px;
    font-size: 18px;
}

/* Glass Box */
.glass-box {
    background: rgba(255,255,255,.08);
    border: 1px solid rgba(255,255,255,.15);
    border-radius: 14px;
    padding: 12px 16px;
    font-size: 14px;
    line-height: 1.7;
    box-shadow: inset 0 2px 6px rgba(0,0,0,.2);
}

/* Purchases */
.purchase-table {
    background: rgba(255,255,255,.08);
    border-radius: 14px;
    padding: 10px;
    font-size: 13px;
}

.purchase-head,
.purchase-row {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1fr;
    text-align: center;
    padding: 8px 0;
}

.purchase-head {
    font-weight: 800;
    color: var(--prime);
    border-bottom: 1px solid rgba(255,255,255,.2);
}

.purchase-row {
    border-bottom: 1px dashed rgba(255,255,255,.15);
}

.purchase-row:last-child {
    border-bottom: none;
}

/* Total */
.invoice-total {
    margin-top: 30px;
    padding: 15px;
    background: linear-gradient(135deg, var(--prime), var(--prime-soft));
    color: var(--bg);
    border-radius: 18px;
    display: flex;
    justify-content: space-between;
    font-size: 20px;
    font-weight: 900;
    box-shadow: 0 10px 30px rgba(0,0,0,.3);
}

/* Footer */
.invoice-footer {
    text-align: center;
    margin-top: 25px;
    color: var(--prime-soft);
    font-weight: 700;
}

/* Anim */
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* Mobile */
@media (max-width: 600px) {
    .purchase-head,
    .purchase-row {
        grid-template-columns: 1.5fr .8fr .8fr .8fr;
        font-size: 12px;
    }
}
</style>
@endsection
