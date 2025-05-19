@extends('layouts.admin')
@section('title', 'Account Transactions')

@push('style')
    @include('import.css.datatable')
@endpush

@section('content')
    <x-breadcumb title="Account Transactions"/>
    <div class="table-responsive">
        <div class="dashboard-card">
            <div class="card-header-section">
                <div class="table-title-section">
                    <div class="table-icon">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <h5 class="table-title">Transaction History</h5>
                </div>
            </div>
            <div class="table-responsive">
                <table id="example2" class="table table-hover">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Transaction ID</th>
                            <th>Store</th>
                            <th>Type</th>
                            <th>Source</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Created By</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5" style="text-align: right;">Total:</th>
                            <th id="totalAmount"></th>
                            <th colspan="2"></th>
                        </tr>
                    </tfoot>
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
                { data: 'transaction_id' },
                { data: 'store', name: 'store.name' },
                { data: 'transaction_type' },
                { data: 'transaction_source' },
                { data: 'amount' },
                { data: 'transaction_date' },
                { data: 'created_by', name: 'createdBy.name' },
            ];

            const table = initDataTable('#example2', '{{ route($role . 'account-transactions.index') }}', columns);

            table.on('draw', function () {
                let total = 0;
                table.rows().data().each(function (value) {
                    let amount = value.amount.toString().replace(/,/g, ''); // Remove commas if present
                    total += Number(amount) || 0;
                });

                $('#totalAmount').text(total.toLocaleString(undefined, { minimumFractionDigits: 2 }));

            });
        }
    </script>
@endpush
