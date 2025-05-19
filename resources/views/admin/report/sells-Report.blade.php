@extends('layouts.admin')
@section('title', 'Sells Report')

@section('content')
    <div class="container mt-4">
        <h1 class="mb-4">Sales Report</h1>

        <!-- Total Sales Metrics with Icons -->
        <div class="row">
            <!-- Total Sales -->
            <div class="col-md-4">
                <div class="card text-white bg-success mb-3">
                    <div class="card-body d-flex align-items-center">

                        <div>
                            <h5 class="card-title"><i class="fas fa-dollar-sign mr-3"></i>Total Sales</h5>
                            <p class="card-text h4">${{ number_format($totalSales, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Total Tax -->
            <div class="col-md-4">
                <div class="card text-white bg-info mb-3">
                    <div class="card-body d-flex align-items-center">

                        <div>
                            <h5 class="card-title"><i class="fas fa-percent mr-3"></i>Total Tax</h5>
                            <p class="card-text h4">${{ number_format($totalTax, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Total Discounts -->
            <div class="col-md-4">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-body d-flex align-items-center">

                        <div>
                            <h5 class="card-title"> <i class="fas fa-tags mr-3"></i>Total Discounts</h5>
                            <p class="card-text h4">${{ number_format($totalDiscount, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row mt-4">
            <!-- Line Chart: Sales Over Time -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-chart-line"></i> Sales Over Time (Last 30 Days)
                    </div>
                    <div class="card-body">
                        <canvas id="salesLineChart"></canvas>
                    </div>
                </div>
            </div>
            <!-- Bar Chart: Sales by Day -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-chart-bar"></i> Daily Sales Breakdown (Last 30 Days)
                    </div>
                    <div class="card-body">
                        <canvas id="salesBarChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Details (Optional) -->
        <div class="row mt-4">
            <div class="col-md-12">
                <h3>Sales Summary</h3>
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Total Sales ($)</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($salesLast30Days as $sale)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($sale->date)->format('M d, Y') }}</td>
                            <td>${{ number_format($sale->total, 2) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Scripts to Render Charts -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Prepare data for the charts
            var salesData = @json($salesLast30Days);
            var dates = salesData.map(item => item.date);
            var totals = salesData.map(item => item.total);

            // Line Chart: Sales Over Time
            var ctxLine = document.getElementById('salesLineChart').getContext('2d');
            var salesLineChart = new Chart(ctxLine, {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: [{
                        label: 'Sales Total',
                        data: totals,
                        borderColor: '#4bc0c0',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        fill: true,
                        borderWidth: 2,
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                        },
                        legend: {
                            display: true,
                            position: 'top',
                        }
                    },
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Date'
                            },
                            ticks: {
                                maxRotation: 45,
                                minRotation: 45
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Total Sales ($)'
                            },
                            beginAtZero: true
                        }
                    }
                }
            });

            // Bar Chart: Daily Sales Breakdown
            var ctxBar = document.getElementById('salesBarChart').getContext('2d');
            var salesBarChart = new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: dates,
                    datasets: [{
                        label: 'Daily Sales',
                        data: totals,
                        backgroundColor: '#36a2eb',
                        borderColor: '#36a2eb',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                        },
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Date'
                            },
                            ticks: {
                                maxRotation: 45,
                                minRotation: 45
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Sales ($)'
                            },
                            beginAtZero: true
                        }
                    }
                }
            });
        </script>
    </div>
@endsection
