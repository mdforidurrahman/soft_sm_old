<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Profit & Loss Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .subtitle {
            color: #666;
            font-size: 14px;
            margin-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-align: left;
        }
        .summary-card {
            float: left;
            width: 23%;
            margin: 1%;
            padding: 10px;
            border: 1px solid #ddd;
            box-sizing: border-box;
        }
        .summary-card h3 {
            margin: 0;
            color: #666;
            font-size: 12px;
        }
        .summary-card .value {
            font-size: 16px;
            font-weight: bold;
            margin: 5px 0;
        }
        .growth {
            font-size: 11px;
        }
        .growth.positive {
            color: green;
        }
        .growth.negative {
            color: red;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            font-size: 10px;
            color: #666;
            text-align: center;
            padding: 10px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Profit & Loss Report</h1>
        <div class="subtitle">
            Period: {{ $period['start'] }} - {{ $period['end'] }}
        </div>
        @if(isset($store))
            <div class="subtitle">Store: {{ $store->name }}</div>
        @else
            <div class="subtitle">All Stores</div>
        @endif
    </div>

    <div class="summary-cards clearfix">
        <div class="summary-card">
            <h3>Gross Sales</h3>
            <div class="value">${{ number_format($report_data['sales']['gross'], 2) }}</div>
            <div class="growth {{ $report_data['sales']['growth'] >= 0 ? 'positive' : 'negative' }}">
                {{ $report_data['sales']['growth'] >= 0 ? '↑' : '↓' }}
                {{ abs($report_data['sales']['growth']) }}%
            </div>
        </div>
        <div class="summary-card">
            <h3>Net Sales</h3>
            <div class="value">${{ number_format($report_data['sales']['net'], 2) }}</div>
            <div class="growth {{ $report_data['sales']['growth'] >= 0 ? 'positive' : 'negative' }}">
                {{ $report_data['sales']['growth'] >= 0 ? '↑' : '↓' }}
                {{ abs($report_data['sales']['growth']) }}%
            </div>
        </div>
        <div class="summary-card">
            <h3>Total Costs</h3>
            <div class="value">${{ number_format($report_data['costs']['total_costs'], 2) }}</div>
            <div class="growth {{ $report_data['costs']['growth'] <= 0 ? 'positive' : 'negative' }}">
                {{ $report_data['costs']['growth'] <= 0 ? '↓' : '↑' }}
                {{ abs($report_data['costs']['growth']) }}%
            </div>
        </div>
        <div class="summary-card">
            <h3>Net Profit</h3>
            <div class="value">${{ number_format($report_data['summary']['net_profit'], 2) }}</div>
            <div class="growth {{ $report_data['summary']['growth'] >= 0 ? 'positive' : 'negative' }}">
                {{ $report_data['summary']['growth'] >= 0 ? '↑' : '↓' }}
                {{ abs($report_data['summary']['growth']) }}%
            </div>
        </div>
    </div>

    <div style="margin-top: 20px;">
        <table>
            <tr>
                <th colspan="2">Sales Overview</th>
            </tr>
            <tr>
                <td>Gross Sales</td>
                <td style="text-align: right">${{ number_format($report_data['sales']['gross'], 2) }}</td>
            </tr>
            <tr>
                <td>Sales Tax</td>
                <td style="text-align: right">${{ number_format($report_data['sales']['tax'], 2) }}</td>
            </tr>
            <tr>
                <td>Discounts</td>
                <td style="text-align: right">-${{ number_format($report_data['sales']['discount'], 2) }}</td>
            </tr>
            <tr>
                <th>Net Sales</th>
                <th style="text-align: right">${{ number_format($report_data['sales']['net'], 2) }}</th>
            </tr>
        </table>

        <table>
            <tr>
                <th colspan="2">Costs Overview</th>
            </tr>
            <tr>
                <td>Gross Purchases</td>
                <td style="text-align: right">${{ number_format($report_data['costs']['purchases']['gross'], 2) }}</td>
            </tr>
            <tr>
                <td>Purchase Tax</td>
                <td style="text-align: right">${{ number_format($report_data['costs']['purchases']['tax'], 2) }}</td>
            </tr>
            <tr>
                <td>Purchase Discounts</td>
                <td style="text-align: right">-${{ number_format($report_data['costs']['purchases']['discount'], 2) }}</td>
            </tr>
            <tr>
                <td>Net Purchases</td>
                <td style="text-align: right">${{ number_format($report_data['costs']['purchases']['net'], 2) }}</td>
            </tr>
            <tr>
                <td>Operating Expenses</td>
                <td style="text-align: right">${{ number_format($report_data['costs']['expenses'], 2) }}</td>
            </tr>
            <tr>
                <th>Total Costs</th>
                <th style="text-align: right">${{ number_format($report_data['costs']['total_costs'], 2) }}</th>
            </tr>
        </table>
    </div>

    <div class="footer">
        Generated on {{ $generated_at }}
    </div>
</body>
</html>
