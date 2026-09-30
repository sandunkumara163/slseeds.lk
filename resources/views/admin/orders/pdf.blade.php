<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Orders Export</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
            word-wrap: break-word;
        }
        th {
            background-color: #4CAF50;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .products-list {
            margin: 0;
            padding-left: 15px;
        }
    </style>
</head>
<body>
    <h2>Orders Report - {{ $date }}</h2>
    
    <table>
        <thead>
            <tr>
                <th width="8%">Order #</th>
                <th width="12%">Date</th>
                <th width="15%">Customer</th>
                <th width="12%">Contact</th>
                <th width="15%">Address</th>
                <th width="20%">Products</th>
                <th width="8%">Total</th>
                <th width="10%">Pay/Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
                @php
                    $customerName = $order->user ? $order->user->name : 'Guest';
                    
                    $address = $order->delivery_address ?? ($order->user ? $order->user->address : 'N/A');
                    $phone = $order->user ? $order->user->phone : 'N/A';
                    
                    if (strpos($address, '| Phone:') !== false) {
                        $parts = explode('| Phone:', $address);
                        $address = trim($parts[0]);
                        $phone = trim($parts[1]);
                    }
                @endphp
                <tr>
                    <td>#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                    <td>{{ $customerName }}</td>
                    <td>{{ $phone }}</td>
                    <td>{{ $address }}</td>
                    <td>
                        <ul class="products-list">
                            @foreach($order->items as $item)
                                @php
                                    $productName = 'Unknown';
                                    if ($item->product && $item->product->translations) {
                                        $translation = $item->product->translations->where('language_code', 'en')->first();
                                        if ($translation) $productName = $translation->name;
                                    }
                                @endphp
                                <li>{{ $productName }} (x{{ $item->quantity }})</li>
                            @endforeach
                        </ul>
                    </td>
                    <td>Rs. {{ number_format($order->total_amount, 2) }}</td>
                    <td>
                        {{ ucfirst($order->payment_method) }}<br>
                        <strong>{{ strtoupper($order->status) }}</strong>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
