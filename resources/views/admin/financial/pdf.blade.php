<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Financial Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { color: #16a34a; margin-bottom: 5px; }
        .summary { display: table; width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .summary-box { display: table-cell; width: 33%; padding: 10px; border: 1px solid #ddd; text-align: center; }
        .summary-box h3 { margin-top: 0; color: #666; font-size: 12px; text-transform: uppercase; }
        .summary-box p { margin: 0; font-size: 18px; font-weight: bold; color: #111; }
        table.data { width: 100%; border-collapse: collapse; }
        table.data th, table.data td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        table.data th { background-color: #f9fafb; font-weight: bold; color: #555; text-transform: uppercase; font-size: 11px; }
        table.data td { font-size: 11px; }
        .text-right { text-align: right !important; }
        .text-red { color: #dc2626; }
        .line-through { text-decoration: line-through; }
    </style>
</head>
<body>

    <div class="header">
        <h1>SLSeeds.lk - Financial Report</h1>
        <p>Period: {{ ucfirst($filter) }}</p>
        <p>Generated on: {{ \Carbon\Carbon::now()->format('Y-m-d H:i') }}</p>
    </div>

    <div class="summary">
        <div class="summary-box">
            <h3>COD Income</h3>
            <p>Rs. {{ number_format($codIncome, 2) }}</p>
        </div>
        <div class="summary-box">
            <h3>Online Income</h3>
            <p>Rs. {{ number_format($onlineIncome, 2) }}</p>
        </div>
        <div class="summary-box">
            <h3>Total Earned</h3>
            <p style="color: #16a34a;">Rs. {{ number_format($totalIncome, 2) }}</p>
        </div>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Date</th>
                <th>Customer</th>
                <th>Method</th>
                <th>Status</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                <td>{{ $order->user->name ?? 'Guest' }}</td>
                <td>{{ strtoupper($order->payment_method) }}</td>
                <td>{{ strtoupper($order->status) }}</td>
                <td class="text-right @if(in_array($order->status, ['refunded', 'cancelled', 'failed'])) text-red line-through @endif">
                    Rs. {{ number_format($order->total_amount, 2) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
