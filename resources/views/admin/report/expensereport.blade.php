<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@extends('layouts.admin')
@section('title', 'Expense Report')

@push('style')
    @include('import.css.datatable')
@endpush

@section('content')
    <div class="container">
        <h3 class="mb-3">Expense Report</h3>

        <!-- Total Stats -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5><i class="fas fa-dollar-sign"></i> Total Expenses</h5>
                        <p class="text-success">${{ number_format($totalAmount, 2) }}</p>
                    </div>
                </div>
            </div>


        </div>

        <!-- Day-Wise Report Table -->
        <h5 class="mb-3">Day-Wise Report</h5>
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Date</th>
                <th>Total Expense</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($dayWise as $day)
                <tr>
                    <td>{{ $day->date }}</td>
                    <td>${{ number_format($day->total, 2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <!-- Customer-Wise Report Table -->
        <h5 class="mb-3">Customer-Wise Report</h5>
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Customer</th>
                <th>Total Expense</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($customerWise as $customer)
                <tr>
                    <td>{{ $customer->contact?->name ?? 'Unknown' }}</td>
                    <td>${{ number_format($customer->total, 2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <!-- Charts -->
        <div class="row">
            <div class="col-md-6">
                <canvas id="dayWiseChart"></canvas>
            </div>
            <div class="col-md-6">
                <canvas id="customerWiseChart"></canvas>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-md-12">
                <h5>Monthly Expense Overview</h5>
                <canvas id="monthlyExpenseChart" width="900" height="300"></canvas>
            </div>
        </div>
        @endsection
@push('script')
        <script>
            // Day-Wise Chart
            const dayWiseData = @json($dayWiseChart);
            const dayWiseCtx = document.getElementById('dayWiseChart').getContext('2d');
            new Chart(dayWiseCtx, {
                type: 'bar',
                data: {
                    labels: dayWiseData.map(data => data.label),
                    datasets: [{
                        label: 'Day-Wise Expenses',
                        data: dayWiseData.map(data => data.value),
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                    }]
                }
            });

            // Customer-Wise Chart
            const customerWiseData = @json($customerWiseChart);
            const customerWiseCtx = document.getElementById('customerWiseChart').getContext('2d');
            new Chart(customerWiseCtx, {
                type: 'pie',
                data: {
                    labels: customerWiseData.map(data => data.label),
                    datasets: [{
                        label: 'Customer-Wise Expenses',
                        data: customerWiseData.map(data => data.value),
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1,
                    }]
                }
            });

            // Monthly Expense Chart
            const monthlyExpenseData = @json($monthlyExpenseChart);
            const monthlyExpenseCtx = document.getElementById('monthlyExpenseChart').getContext('2d');
            new Chart(monthlyExpenseCtx, {
                type: 'line',
                data: {
                    labels: monthlyExpenseData.map(data => data.label),
                    datasets: [{
                        label: 'Monthly Expenses',
                        data: monthlyExpenseData.map(data => data.value),
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 2,
                        fill: true,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Monthly Expense Overview'
                        }
                    }
                }
            });
        </script>
@endpush
