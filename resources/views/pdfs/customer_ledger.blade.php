<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Customer Ledger - {{ $customer->name }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <h2>Customer Ledger</h2>
    <h3>{{ $customer->name }}</h3>
    <p>Date Range: {{ $dateRange }}</p>
    
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Reference</th>
                <th>Amount</th>
                <th>Payment Method</th>
                <th>Status</th>
                <th>Location</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
            <tr>
                <td>{{ $transaction['date']->format('m/d/Y') }}</td>
                <td>{{ $transaction['type'] }}</td>
                <td>{{ $transaction['reference_no'] }}</td>
                <td class="text-right">{{ number_format($transaction['amount'], 2) }}</td>
                <td>{{ $transaction['payment_method'] ?? '-' }}</td>
                <td>{{ $transaction['payment_status'] }}</td>
                <td>{{ $transaction['location'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <h4>Summary</h4>
    <p>Total Invoice Amount: {{ number_format($summary['total_invoice'], 2) }}</p>
    <p>Total Paid: {{ number_format($summary['total_paid'], 2) }}</p>
    <p>Balance Due: {{ number_format($summary['balance_due'], 2) }}</p>
</body>
</html>