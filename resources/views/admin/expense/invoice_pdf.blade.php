<!DOCTYPE html>
<html>
<head>
    <title>Invoice {{ $expense->reference_no }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .invoice-header { text-align: center; margin-bottom: 20px; }
        .invoice-details { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
<div class="invoice-header">
    <h1>Invoice</h1>
    <p>Invoice No: {{ $expense->reference_no }}</p>
</div>

<div class="invoice-details">
    <div>
        <strong>From:</strong> {{ $expense->store->name }}<br>
        {{ $expense->store->address }}
    </div>
    <div>
        <strong>Bill To:</strong> {{ $expense->user->name }}<br>
        {{ $expense->user->email }}<br>
        {{ $expense->user->phone }}
    </div>
</div>

<table>
    <thead>
    <tr>
        <th>Description</th>
        <th>Amount</th>
        <th>Tax</th>
        <th>Total</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>{{ $expense->expenseCategory->name }}</td>
        <td class="text-right">${{ number_format($expense->total_amount, 2) }}</td>
        <td class="text-center">Included</td>
        <td class="text-right">${{ number_format($expense->total_amount, 2) }}</td>
    </tr>
    </tbody>
</table>

<div class="invoice-footer">
    <p>Notes: {{ $expense->note ?? 'No additional notes' }}</p>
</div>
</body>
</html>