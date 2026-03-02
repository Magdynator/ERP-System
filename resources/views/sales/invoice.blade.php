<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $sale->sale_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 14px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        .header {
            width: 100%;
            margin-bottom: 40px;
        }
        .header td {
            vertical-align: top;
        }
        .company-info h1 {
            color: #111827;
            font-size: 28px;
            margin: 0 0 5px 0;
            letter-spacing: -1px;
        }
        .company-info p {
            color: #6B7280;
            margin: 0;
            font-size: 12px;
        }
        .invoice-details {
            text-align: right;
        }
        .invoice-details h2 {
            color: #6366f1;
            font-size: 24px;
            margin: 0 0 10px 0;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .invoice-details p {
            margin: 0 0 5px 0;
            font-size: 13px;
        }
        .invoice-details strong {
            color: #111827;
        }
        .bill-to {
            margin-bottom: 40px;
            padding: 15px;
            background: #f9fafb;
            border-left: 4px solid #6366f1;
        }
        .bill-to h3 {
            margin: 0 0 10px 0;
            font-size: 13px;
            text-transform: uppercase;
            color: #6B7280;
            letter-spacing: 1px;
        }
        .bill-to p {
            margin: 0;
            font-size: 15px;
            color: #111827;
            font-weight: bold;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th {
            background-color: #f3f4f6;
            color: #374151;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 12px;
            text-align: left;
            border-bottom: 2px solid #e5e7eb;
        }
        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
            color: #111827;
        }
        .items-table .text-right {
            text-align: right;
        }
        .product-name {
            font-weight: bold;
        }
        .summary-box {
            width: 300px;
            float: right;
            border-top: 2px solid #111827;
            padding-top: 15px;
        }
        .summary-row {
            width: 100%;
            margin-bottom: 10px;
        }
        .summary-row td {
            padding: 5px 0;
            font-size: 14px;
        }
        .summary-total {
            font-size: 18px;
            font-weight: bold;
            color: #111827;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px !important;
        }
        .payments {
            clear: both;
            padding-top: 50px;
        }
        .payments h3 {
            font-size: 13px;
            text-transform: uppercase;
            color: #6B7280;
            letter-spacing: 1px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        .payment-item {
            font-size: 13px;
            color: #4b5563;
            margin-bottom: 5px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 11px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }
    </style>
</head>
<body>

    <table class="header">
        <tr>
            <td class="company-info" style="width: 50%;">
                <h1>ULTRA ERP</h1>
                <p>123 Business Avenue, Suite 100</p>
                <p>Metropolis, NY 10001</p>
                <p>contact@ultra-erp.test</p>
            </td>
            <td class="invoice-details" style="width: 50%;">
                <h2>INVOICE</h2>
                <p>Date: <strong>{{ $sale->sale_date->format('M d, Y') }}</strong></p>
                <p>Reference: <strong>{{ $sale->sale_number }}</strong></p>
                @if($sale->status)
                <p>Status: <strong>{{ strtoupper($sale->status) }}</strong></p>
                @endif
            </td>
        </tr>
    </table>

    <div class="bill-to">
        <h3>Bill To</h3>
        <p>{{ $sale->customer_name ?? 'Walk-in Customer' }}</p>
        @if($sale->customer_email)
        <p style="font-size: 13px; font-weight: normal; color: #6B7280;">{{ $sale->customer_email }}</p>
        @endif
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Item Description</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Price</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $item)
            <tr>
                <td class="product-name">
                    Product #{{ $item->product_id }}
                </td>
                <td class="text-right">{{ $item->quantity }}</td>
                <td class="text-right">{{ number_format($item->selling_price, 2) }}</td>
                <td class="text-right">{{ number_format($item->quantity * $item->selling_price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="summary-box">
        <tr class="summary-row">
            <td style="color: #6B7280;">Subtotal:</td>
            <td class="text-right">{{ number_format($sale->total, 2) }} {{ $sale->currency }}</td>
        </tr>
        <tr class="summary-row">
            <td class="summary-total">Total:</td>
            <td class="text-right summary-total">{{ number_format($sale->total, 2) }} {{ $sale->currency }}</td>
        </tr>
    </table>

    @if($sale->payments->count() > 0)
    <div class="payments">
        <h3>Payment History</h3>
        @foreach($sale->payments as $payment)
        <div class="payment-item">
            &bull; {{ number_format($payment->amount, 2) }} {{ $sale->currency }} via <strong>{{ ucfirst($payment->method) }}</strong>
            @if($payment->reference) (Ref: {{ $payment->reference }}) @endif
        </div>
        @endforeach
    </div>
    @endif

    <div class="footer">
        Thank you for your business. This is a computer-generated document. No signature is required.
    </div>

</body>
</html>
