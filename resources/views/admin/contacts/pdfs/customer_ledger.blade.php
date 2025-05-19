<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Ledger - {{ $customer['name'] }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Kalpurush&display=swap" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Kalpurush&display=swap');

        body {
            font-family: 'Kalpurush', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }

        .header {
            background-color: #2c3e50;
            color: white;
            text-align: center;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .summary-section {
            background-color: white;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            overflow: hidden;
        }

        th, td {
            border: 1px solid #e0e0e0;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #3498db;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .no-transactions {
            text-align: center;
            color: #7f8c8d;
            padding: 20px;
        }
    </style>

</head>
<body>
<div class="header">
    <h1>Customer Ledger</h1>
    <h2>{{ $customer['name'] }}</h2>
</div>

<div class="summary-section">
    <div class="summary-grid">
        <div>
            <strong>Date Range:</strong> {{ $dateRange }}
        </div>
        <div>
            <strong>Customer ID:</strong> {{ $customer['id'] }}
        </div>
        <div>
            <strong>Address:</strong> {{ $customer['address'] }}
        </div>
        <div>
            <strong>Phone:</strong> {{ $customer['phone'] }}
        </div>
    </div>
</div>

<div class="summary-section">
    <h3>Financial Summary</h3>
    <div class="summary-grid">
        <div>
            <strong>Total Invoice:</strong> ${{ number_format($summary['overall_invoice'], 2) }}
        </div>
        <div>
            <strong>Total Paid:</strong> ${{ number_format($summary['overall_paid'], 2) }}
        </div>
        <div>
            <strong>Balance Due:</strong> ${{ number_format($summary['balance_due'], 2) }}
        </div>
    </div>
</div>

<h3>Transactions</h3>
@if(count($transactions) > 0)
    <table>
        <thead>
        <tr>
            <th>Date</th>
            <th>Location</th>
            <th>Debit</th>
            <th>Credit</th>
            <th>Reference No</th>
            <th>Type</th>

        </tr>
        </thead>
        <tbody>
        @foreach($transactions as $transaction)
            <tr>
                <td>{{ $transaction['date'] }}</td>
                <td>{{ $transaction['location'] }}</td>
                <td>${{ number_format($transaction['debit'], 2) }}</td>
                <td>${{ number_format($transaction['credit'], 2) }}</td>
                <td>{{ $transaction['reference_no'] ?? '-' }}</td>
                <td>{{ $transaction['type'] }}</td>

            </tr>
        @endforeach
        </tbody>
    </table>
@else
    <div class="no-transactions">
        No transactions found for this period.
    </div>
@endif
</body>
</html>