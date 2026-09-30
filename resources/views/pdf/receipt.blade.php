<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 14px;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #16a34a;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #16a34a;
            margin: 0;
            font-size: 28px;
            letter-spacing: 1px;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
            font-size: 12px;
        }
        .details-container {
            width: 100%;
            margin-bottom: 30px;
        }
        .details-container td {
            vertical-align: top;
            width: 50%;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #16a34a;
            margin-bottom: 10px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        .info-p {
            margin: 0 0 5px;
        }
        .status {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-completed { background-color: #dcfce7; color: #166534; }
        .status-pending { background-color: #fef08a; color: #854d0e; }
        .status-processing { background-color: #dbeafe; color: #1e40af; }
        .status-cancelled { background-color: #f3f4f6; color: #374151; }
        .status-shipped { background-color: #e0e7ff; color: #3730a3; }
        .status-delivered { background-color: #ccfbf1; color: #115e59; }
        .status-refunded { background-color: #f3e8ff; color: #6b21a8; }
        .status-on_hold { background-color: #ffedd5; color: #9a3412; }
        .status-failed { background-color: #fee2e2; color: #991b1b; }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th {
            background-color: #f9fafb;
            color: #4b5563;
            font-size: 12px;
            text-transform: uppercase;
            text-align: left;
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        .items-table th.text-right,
        .items-table td.text-right {
            text-align: right;
        }
        .items-table td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 8px 10px;
            text-align: right;
        }
        .totals-table td.label {
            color: #6b7280;
            width: 70%;
        }
        .totals-table td.value {
            font-weight: bold;
        }
        .grand-total {
            background-color: #f0fdf4;
            color: #166534;
            font-size: 18px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
    </style>
</head>
<body>

    <div class="header">
            <img src="{{ public_path('images/logo.jpg') }}" alt="SLSeeds Logo" style="height: 50px; border-radius: 50%; margin-bottom: 10px;">
            <h1>SLSeeds.lk</h1>
        <p>Premium quality seeds delivered to your doorstep.</p>
    </div>

    <table class="details-container">
        <tr>
            <td>
                <div class="section-title">Order Information</div>
                <p class="info-p"><strong>Order ID:</strong> #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</p>
                <p class="info-p"><strong>Date:</strong> {{ $order->created_at->format('F d, Y h:i A') }}</p>
                <p class="info-p"><strong>Payment Method:</strong> {{ strtoupper($order->payment_method) }}</p>
                @if($order->payment_method === 'payhere' && $order->transaction_id)
                <p class="info-p"><strong>Payment No:</strong> {{ $order->transaction_id }}</p>
                @endif
                <p class="info-p">
                    <strong>Status:</strong> 
                    <span class="status status-{{ $order->status }}">{{ str_replace('_', ' ', $order->status) }}</span>
                </p>
            </td>
            <td>
                <div class="section-title">Customer Details</div>
                <p class="info-p"><strong>Name:</strong> {{ $order->user->name }}</p>
                <p class="info-p"><strong>Email:</strong> {{ $order->user->email }}</p>
                <p class="info-p" style="white-space: pre-line;"><strong>Delivery To:</strong><br>{{ $order->delivery_address }}</p>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th>Product Description</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @php $subtotal = 0; @endphp
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product->translation('en')->name ?? 'Seed' }}</td>
                <td class="text-right">Rs. {{ number_format($item->price, 2) }}</td>
                <td class="text-right">{{ $item->quantity }}</td>
                <td class="text-right">Rs. {{ number_format($item->price * $item->quantity, 2) }}</td>
            </tr>
            @php $subtotal += ($item->price * $item->quantity); @endphp
            @endforeach
        </tbody>
    </table>

    <table class="totals-table">
        <tr>
            <td class="label">Subtotal</td>
            <td class="value">Rs. {{ number_format($subtotal, 2) }}</td>
        </tr>
        <tr>
            <td class="label">Delivery Fee</td>
            <td class="value">Rs. {{ number_format($order->total_amount - $subtotal, 2) }}</td>
        </tr>
        <tr class="grand-total">
            <td class="label" style="color: #166534; font-weight: bold;">Grand Total</td>
            <td class="value">Rs. {{ number_format($order->total_amount, 2) }}</td>
        </tr>
    </table>

    <div class="footer">
        <p>Thank you for shopping with SLSeeds.lk!</p>
        <p>If you have any questions about this receipt, please contact us at support@slseeds.lk</p>
    </div>

</body>
</html>
