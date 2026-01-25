<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>الفاتورة</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        :root {
            --prime: #ddcdbc;
            --prime-soft: #e6ddd4;
            --bg: #515831;
            --bg-dark: #3f4526;
            --white: #ffffff;
            --border: rgba(221, 205, 188, .35);
        }

        body {
            margin: 0;
            font-family: system-ui, sans-serif;
            background: linear-gradient(180deg, var(--bg), var(--bg-dark));
            color: var(--white);
        }

        .page {
            max-width: 1100px;
            margin: auto;
            padding: 24px;
        }

        .invoice-box {
            background: rgba(221, 205, 188, 0.08);
            backdrop-filter: blur(12px);
            border-radius: 18px;
            padding: 22px;
            border: 1px solid var(--border);
            box-shadow: 0 20px 40px rgba(0, 0, 0, .35);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
        }

        .header h2 {
            color: var(--prime);
            font-size: 22px;
            font-weight: 900;
        }

        .back-btn {
            background: linear-gradient(135deg, var(--prime), var(--prime-soft));
            color: var(--bg);
            border-radius: 12px;
            padding: 8px 14px;
            font-weight: 700;
            border: none;
            cursor: pointer;
        }

        .summary {
            font-size: 18px;
            font-weight: 900;
            color: var(--prime);
            margin-bottom: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            background: rgba(255, 255, 255, .03);
            border-radius: 12px;
            overflow: hidden;
        }

        thead {
            background: rgba(221, 205, 188, .15);
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid var(--border);
            text-align: center;
        }

        th {
            color: var(--prime);
            font-weight: 800;
        }

        td.name {
            text-align: right;
            font-weight: 700;
        }

        td.total {
            color: #b6ffb6;
            font-weight: 900;
        }

        .actions {
            display: flex;
            justify-content: space-between;
            margin-top: 18px;
            gap: 10px;
            flex-wrap: wrap;
        }

        button {
            border: none;
            border-radius: 14px;
            padding: 10px 18px;
            font-weight: 800;
            cursor: pointer;
        }

        .btn-cancel {
            background: rgba(255, 255, 255, .15);
            color: var(--white);
        }

        .btn-print {
            background: var(--prime-soft);
            color: var(--bg);
        }

        .btn-done {
            background: linear-gradient(135deg, var(--prime), var(--prime-soft));
            color: var(--bg);
        }

        .btn-done:hover {
            transform: scale(1.05);
        }

        /* Mobile cards */
        @media (max-width: 640px) {
            table {
                display: none;
            }

            .cards {
                display: grid;
                gap: 12px;
            }

            .card-item {
                background: rgba(255, 255, 255, .05);
                border: 1px solid var(--border);
                border-radius: 14px;
                padding: 14px;
                display: flex;
                justify-content: space-between;
            }

            .qty {
                background: var(--prime);
                color: var(--bg);
                border-radius: 8px;
                padding: 4px 10px;
                font-weight: 900;
            }

            .name {
                font-weight: 800;
                color: var(--prime);
            }

            .price {
                font-size: 13px;
                color: #ddd;
            }

            .total {
                color: #b6ffb6;
                font-weight: 900;
            }
        }
    </style>
</head>

<body>

    <div class="page">

        <div class="header">
            <button class="back-btn" onclick="history.back()">← رجوع</button>
            <h2>الفاتورة</h2>
        </div>

        <div class="invoice-box">

            @php $grandTotal = 0; @endphp
            @foreach ($items as $item)
                @php $grandTotal += $item['total']; @endphp
            @endforeach

            <div class="summary">
                الإجمالي: {{ $grandTotal }} جنيه
            </div>

            <!-- جدول الديسكتوب -->
            <table>
                <thead>
                    <tr>
                        <th>عدد</th>
                        <th>المنتج</th>
                        <th>السعر</th>
                        <th>الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                        <tr>
                            <td>{{ $item['qty'] }}</td>
                            <td class="name">{{ $item['name'] }}</td>
                            <td>{{ $item['price'] ?? '-' }}</td>
                            <td class="total">{{ $item['total'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- كروت الموبايل -->
            <div class="cards">
                @foreach ($items as $item)
                    <div class="card-item">
                        <div class="card-left">
                            <div class="qty">{{ $item['qty'] }}</div>
                            <div>
                                <div class="name">{{ $item['name'] }}</div>
                                <div class="price">سعر الوحدة: {{ $item['price'] ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="total">{{ $item['total'] }}</div>
                    </div>
                @endforeach
            </div>

            <!-- فورم الإرسال -->
            <form id="invoiceForm" style="margin-top:12px;">
                @csrf

                @foreach ($items as $index => $item)
                    <input type="hidden" name="items[{{ $index }}][item_type]" value="product">
                    <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $item['id'] }}">
                    <input type="hidden" name="items[{{ $index }}][qty]" value="{{ $item['qty'] }}">
                @endforeach


                <div class="actions">
                    <div>
                        <button type="button" class="btn-cancel"
                            onclick="window.location.replace('{{ route('sale_proccess.create') }}')">
                            إلغاء
                        </button>

                        <button type="button" class="btn-print" onclick="openPrintForm()">
                            طباعة
                        </button>
                    </div>

                    <button type="submit" class="btn-done">
                        إتمام الفاتورة
                    </button>
                </div>
            </form>

            <!-- فورم الطباعة -->
            <form id="printForm" action="{{ route('invoices.print') }}" method="POST" target="_blank"
                style="display:none;">
                @csrf
                @foreach ($items as $index => $item)
                    <input type="hidden" name="items[{{ $index }}][qty]" value="{{ $item['qty'] }}">
                    <input type="hidden" name="items[{{ $index }}][name]" value="{{ $item['name'] }}">
                    <input type="hidden" name="items[{{ $index }}][price]" value="{{ $item['price'] ?? '' }}">
                @endforeach
            </form>

        </div>
    </div>

    <script>
        function openPrintForm() {
            document.getElementById('printForm').submit();
        }

        document.getElementById('invoiceForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const res = await fetch("{{ route('invoices.store') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json',
                },
                body: new FormData(this)
            });

            const data = await res.json();

            if (res.ok) {
                alert('تم إنشاء الفاتورة بنجاح');
                window.location.href = "{{ route('main.create') }}";
            } else {
                alert(data.message || 'حدث خطأ');
            }
        });
    </script>

</body>

</html>
