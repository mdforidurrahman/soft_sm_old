@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="page-header pb-3">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Profit & Loss Report</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route($role . 'dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Profit & Loss Report</li>
                    </ul>
                </div>
                {{-- @if (isset($response))
                    <div class="col-auto float-end ms-auto">
                        <button onclick="document.getElementById('exportPdfForm').submit();"
                            class="btn btn-outline-primary me-1">
                            <i class="fas fa-file-pdf"></i> Export PDF
                        </button>
                        <button onclick="document.getElementById('exportExcelForm').submit();"
                            class="btn btn-outline-primary">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </button>
                    </div>
                @endif --}}
            </div>
        </div>

        <!-- Filters Card -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="POST" action="{{ route($role . 'reports.profit-loss.data') }}" class="row g-3">
                    @csrf
                    <div class="col-md-3">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control"
                            value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control"
                            value="{{ request('end_date', now()->endOfMonth()->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Store</label>
                        <select name="store_id" class="form-control select2">
                            <option value="">All Stores</option>
                            @foreach ($stores as $store)
                                <option value="{{ $store->id }}"
                                    {{ request('store_id') == $store->id ? 'selected' : '' }}>
                                    {{ $store->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-sync"></i> Generate Report
                        </button>
                        <button type="reset" class="btn btn-secondary">
                            <i class="fas fa-undo"></i> Reset
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if (isset($response))
            <!-- Report Content -->
            <div class="row">
                <!-- Summary Cards -->
                <div class="col-md-12 mb-4">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title text-muted mb-0">Gross Sales</h6>
                                    <h2 class="mt-2 mb-0">{{ number_format($response['sales']['gross'], 2) }}</h2>
                                    <span class="text-{{ $response['sales']['growth'] >= 0 ? 'success' : 'danger' }}">
                                        <i class="fas fa-arrow-{{ $response['sales']['growth'] >= 0 ? 'up' : 'down' }}"></i>
                                        {{ $response['sales']['growth'] }}%
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title text-muted mb-0">Net Sales</h6>
                                    <h2 class="mt-2 mb-0">{{ number_format($response['sales']['net'], 2) }}</h2>
                                    <span class="text-{{ $response['sales']['growth'] >= 0 ? 'success' : 'danger' }}">
                                        <i
                                            class="fas fa-arrow-{{ $response['sales']['growth'] >= 0 ? 'up' : 'down' }}"></i>
                                        {{ $response['sales']['growth'] }}%
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title text-muted mb-0">Total Costs</h6>
                                    <h2 class="mt-2 mb-0">{{ number_format($response['costs']['total_costs'], 2) }}</h2>
                                    <span class="text-{{ $response['costs']['growth'] >= 0 ? 'danger' : 'success' }}">
                                        <i
                                            class="fas fa-arrow-{{ $response['costs']['growth'] >= 0 ? 'up' : 'down' }}"></i>
                                        {{ $response['costs']['growth'] }}%
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title text-muted mb-0">Net Profit</h6>
                                    <h2 class="mt-2 mb-0">{{ number_format($response['summary']['net_profit'], 2) }}</h2>
                                    <span class="text-{{ $response['summary']['growth'] >= 0 ? 'success' : 'danger' }}">
                                        <i
                                            class="fas fa-arrow-{{ $response['summary']['growth'] >= 0 ? 'up' : 'down' }}"></i>
                                        {{ $response['summary']['growth'] }}%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Report Cards -->
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Sales Overview</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>
                                            <td>Gross Sales</td>
                                            <td class="text-end">{{ number_format($response['sales']['gross'], 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Sales Tax</td>
                                            <td class="text-end">{{ number_format($response['sales']['tax'], 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Discounts</td>
                                            <td class="text-end text-danger">
                                                {{ number_format($response['sales']['discount'], 2) }}</td>
                                        </tr>
                                        <tr class="border-top">
                                            <td><strong>Net Sales</strong></td>
                                            <td class="text-end">
                                                <strong>{{ number_format($response['sales']['net'], 2) }}</strong>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Costs Overview</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>
                                            <td>Gross Purchases</td>
                                            <td class="text-end">
                                                {{ number_format($response['costs']['purchases']['gross'], 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Purchase Tax</td>
                                            <td class="text-end">
                                                {{ number_format($response['costs']['purchases']['tax'], 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Purchase Discounts</td>
                                            <td class="text-end text-success">
                                                {{ number_format($response['costs']['purchases']['discount'], 2) }}</td>
                                        </tr>
                                        <tr class="border-top">
                                            <td>Net Purchases</td>
                                            <td class="text-end">
                                                {{ number_format($response['costs']['purchases']['net'], 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Operating Expenses</td>
                                            <td class="text-end">{{ number_format($response['costs']['expenses'], 2) }}
                                            </td>
                                        </tr>
                                        <tr class="border-top">
                                            <td><strong>Total Costs</strong></td>
                                            <td class="text-end">
                                                <strong>{{ number_format($response['costs']['total_costs'], 2) }}</strong>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profit/Loss Chart -->
                <div class="col-md-12 mt-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Profit/Loss Trend</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="profitLossChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hidden Export Forms -->
            <form id="exportPdfForm" action="{{ route($role . 'reports.profit-loss.export-pdf') }}" method="POST"
                class="d-none">
                @csrf
                <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                <input type="hidden" name="store_id" value="{{ request('store_id') }}">
            </form>

            <form id="exportExcelForm" action="{{ route($role . 'reports.profit-loss.export-excel') }}" method="POST"
                class="d-none">
                @csrf
                <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                <input type="hidden" name="store_id" value="{{ request('store_id') }}">
            </form>
        @endif
    </div>

    @push('script')
        @if (isset($response))
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                // Initialize the chart with the data
                const ctx = document.getElementById('profitLossChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($response['chart_data']['labels']) !!},
                        datasets: [{
                            label: 'Sales',
                            data: {!! json_encode($response['chart_data']['sales']) !!},
                            borderColor: '#28a745',
                            fill: false
                        }, {
                            label: 'Costs',
                            data: {!! json_encode($response['chart_data']['costs']) !!},
                            borderColor: '#dc3545',
                            fill: false
                        }, {
                            label: 'Profit',
                            data: {!! json_encode($response['chart_data']['profit']) !!},
                            borderColor: '#007bff',
                            fill: false
                        }]
                    },
                    options: {
                        responsive: true,
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            </script>
        @endif
        <script>
            $(document).ready(function() {
                $('.select2').select2();
            });
        </script>
    @endpush
@endsection
