@extends('layouts.admin')
@section('title', 'Account Transactions Summary')

@push('style')
    @include('import.css.datatable')
@endpush

@section('content')
    <x-breadcumb title="Account Transactions Summary"/>
    <div class="table-responsive">
        <div class="dashboard-card">
            <div class="card-header-section">
                <div class="table-title-section">
                    <div class="table-icon">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <h5 class="table-title">Daily Transaction Summary</h5>
                </div>
            </div>
            <div class="table-responsive">
                <table id="example2" class="table table-hover">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Date</th>
                            <th>Income ( sells + Collection + Other )</th>
                            <th>Expense ( Discount + All Petty Cash) </th>
                            <th>Bank Withdrawal</th>
                            <th>Adjustment</th>
                            <th>Net Amount</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('script')
    @include('import.js.datatable')

    <script>
        loadTable();

        function loadTable() {
            const columns = [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'date' },
                { data: 'income', render: $.fn.dataTable.render.number(',', '.', 2) },
                { data: 'expense', render: $.fn.dataTable.render.number(',', '.', 2) },
                { data: 'withdrawal', render: $.fn.dataTable.render.number(',', '.', 2) },
                { data: 'adjustment', render: $.fn.dataTable.render.number(',', '.', 2) },
                { 
                    data: 'calculation', 
                    orderable: false, 
                    searchable: false,
                    render: $.fn.dataTable.render.number(',', '.', 2)
                },
            ];

            initDataTable('#example2', '{{ route("admin.account-transactions.transactions-summary") }}', columns, {
                "footerCallback": function (row, data, start, end, display) {
                    var api = this.api();
                    
                    // Calculate totals for each column
                    var incomeTotal = api.column(2).data().reduce(function (a, b) {
                        return parseFloat(a) + parseFloat(b);
                    }, 0);
                    
                    var expenseTotal = api.column(3).data().reduce(function (a, b) {
                        return parseFloat(a) + parseFloat(b);
                    }, 0);
                    
                    var withdrawalTotal = api.column(4).data().reduce(function (a, b) {
                        return parseFloat(a) + parseFloat(b);
                    }, 0);
                    
                    var adjustmentTotal = api.column(5).data().reduce(function (a, b) {
                        return parseFloat(a) + parseFloat(b);
                    }, 0);
                    
                    var netTotal = incomeTotal - (expenseTotal + withdrawalTotal + adjustmentTotal);

                    // Update footer
                    $(api.column(2).footer()).html('Total: ' + incomeTotal.toFixed(2));
                    $(api.column(3).footer()).html('Total: ' + expenseTotal.toFixed(2));
                    $(api.column(4).footer()).html('Total: ' + withdrawalTotal.toFixed(2));
                    $(api.column(5).footer()).html('Total: ' + adjustmentTotal.toFixed(2));
                    $(api.column(6).footer()).html('Net Total: ' + netTotal.toFixed(2));
                }
            });
        }
    </script>
@endpush