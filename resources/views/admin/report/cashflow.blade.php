<style>
.cashflow-report {
    font-family: Arial, sans-serif;
    max-width: 600px;
    margin: 0 auto;
}

.cashflow-report table {
    width: 100%;
    border-collapse: collapse;
}

.cashflow-report td {
    padding: 8px 12px;
    border: 1px solid #ddd;
}

.cashflow-report .total-row {
    border-top: 2px solid #333;
}

.cashflow-report .final-total {
    border-top: 2px solid #333;
    background-color: #f5f5f5;
    font-weight: bold;
}
</style>

<div class="cashflow-report">
    <h3>Cash Flow Report ({{ $startDate }} to {{ $endDate }})</h3>
    
    <table class="table table-bordered">
        <tr>
            <td><strong>Total Sales:</strong></td>
            <td>${{ $report['Total Sales'] }}</td>
        </tr>
        <tr>
            <td>&nbsp;&nbsp;- Cash Sales:</td>
            <td>${{ $report['Cash Sales'] }}</td>
        </tr>
        <tr>
            <td>&nbsp;&nbsp;- Credit Sales:</td>
            <td>${{ $report['Credit Sales'] }}</td>
        </tr>
        <tr>
            <td><strong>Payments Against Previous Dues:</strong></td>
            <td>${{ $report['Payments Against Previous Dues'] }}</td>
        </tr>
        <tr class="total-row">
            <td><strong>Total Cash Received:</strong></td>
            <td>${{ $report['Total Cash Received'] }}</td>
        </tr>
        <tr>
            <td><strong>Expenses:</strong></td>
            <td>-${{ $report['Expenses'] }}</td>
        </tr>
        <tr>
            <td><strong>Bank Withdrawals:</strong></td>
            <td>-${{ $report['Bank Withdrawals'] }}</td>
        </tr>
        <tr class="final-total">
            <td><strong>Final Cash in Hand:</strong></td>
            <td>${{ $report['Final Cash in Hand'] }}</td>
        </tr>
    </table>
</div>