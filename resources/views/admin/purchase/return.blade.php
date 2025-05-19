@extends('layouts.admin')
@section('title', 'Purchase Returns List')

@push('style')
    @include('import.css.datatable')
@endpush

@section('content')
    <x-breadcumb title="Purchase Returns List" />
    <div class="table-responsive">
        <div class="dashboard-card">
            <div class="card-header-section">
                <div class="table-title-section">
                    <div class="table-icon">
                        <i class="fas fa-undo-alt"></i>
                    </div>
                    <h5 class="table-title">Purchase Returns Overview</h5>
                </div>

            </div>
            <div class="table-responsive">
                <table id="example2" class="table table-hover">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Return Date</th>
                            <th>Reference No</th>
                            <th>Supplier</th>
                            <th>Total Return Amount</th>
                            <th>Total Items</th>
                            <th>Status</th>
                            {{-- <th>Action</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
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
            if ($.fn.DataTable.isDataTable('#example2')) {
                $('#example2').DataTable().destroy();
            }

            $('#example2').DataTable({
                lengthChange: true,
                dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                    "<'row'<'col-sm-12'B>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                buttons: [{
                        extend: 'copy',
                        text: '<i class="fas fa-copy"></i> Copy',
                        className: 'btn-export'
                    },
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        className: 'btn-export'
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        className: 'btn-export'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Print',
                        className: 'btn-export'
                    }
                ],
                processing: true,
                serverSide: true,
                ajax: '{{ route($role . 'purchase.return.index') }}',
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'return_date',
                        name: 'return_date'
                    },
                    {
                        data: 'purchase_reference',
                        name: 'purchase_reference'
                    },
                    {
                        data: 'supplier',
                        name: 'supplier'
                    },
                    {
                        data: 'total_return_amount',
                        name: 'total_return_amount'
                    },
                    {
                        data: 'total_items',
                        name: 'total_items'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    // {
                    //     data: 'action',
                    //     name: 'action',
                    //     orderable: false,
                    //     searchable: false
                    // }
                ],
                language: {
                    processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
                    emptyTable: '<div class="text-center p-4"><i class="fas fa-box-open fa-3x text-muted"></i><p class="mt-2">No purchase returns available</p></div>',
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    search: '<i class="fas fa-search"></i>',
                    searchPlaceholder: "Search purchase returns..."
                },
                pageLength: 10,
                order: [
                    [1, 'desc']
                ]
            });
        }
    </script>
@endpush
