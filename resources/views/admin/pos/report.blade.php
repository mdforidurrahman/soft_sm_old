@extends('layouts.admin')
@section('title', 'POS Report')

@push('style')
    @include('import.css.datatable')
@endpush

@section('content')
    <x-breadcumb title="POS Report"/>
    <div class="table-responsive">
        <div class="dashboard-card">
            <div class="card-header-section">
                <div class="table-title-section">
                    <div class="table-icon">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <h5 class="table-title">POS Report</h5>
                </div>
            </div>
            <div class="table-responsive">
                <table id="posReportTable" class="table table-hover">
                    <thead>
                    <tr>
                        <th>SL</th>
                        <th>Contact Name</th>
                        <th>Store Name</th>
                        <th>Seller Name</th>
                        <th>Products (with Quantity)</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Discount</th>
                        <th>Order Tax</th>
                        <th>Shipping Cost</th>
                        <th>Total</th>
                        <th>Payment Method</th>
                        <th>Transaction Date</th>
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
        $(document).ready(function() {
            $('#posReportTable').DataTable({
                lengthChange: true,
                dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                    "<'row'<'col-sm-12'B>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                buttons: [
                    {
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
                        className: 'btn-export',
                        customize: function (doc) {
                            // Set page orientation to landscape for more space
                            doc.pageOrientation = 'landscape';

                            // Adjust column widths to prevent cropping
                            doc.content[1].table.widths = [
                                '5%',   // SL
                                '12%',  // Contact Name
                                '12%',  // Store Name
                                '10%',  // Seller Name
                                '20%',  // Products (with Quantity)
                                '5%',   // Quantity
                                '8%',   // Subtotal
                                '5%',   // Discount
                                '5%',   // Order Tax
                                '5%',   // Shipping Cost
                                '8%',   // Total
                                '8%',   // Payment Method
                                '12%'   // Transaction Date
                            ];

                            // Adjust font size for better readability
                            doc.defaultStyle.fontSize = 8;

                            // Ensure text wraps correctly within columns
                            doc.styles.tableHeader.alignment = 'center';
                            doc.styles.tableBodyEven.alignment = 'left';
                            doc.styles.tableBodyOdd.alignment = 'left';

                            // Set custom margins
                            doc.pageMargins = [20, 20, 20, 20]; // left, top, right, bottom
                        }
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Print',
                        className: 'btn-export'
                    }
                ],
                processing: true,
                serverSide: true,
                ajax: '{{ route($role . "pos.report") }}',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'contact_id', name: 'contact_id' },
                    { data: 'store_name', name: 'store_name' },
                    { data: 'seller_name', name: 'seller_name' },
                    { data: 'product_names', name: 'product_names' },
                    { data: 'quantity', name: 'quantity' },
                    { data: 'subtotal', name: 'subtotal' },
                    { data: 'discount', name: 'discount' },
                    { data: 'order_tax', name: 'order_tax' },
                    { data: 'shipping_cost', name: 'shipping_cost' },
                    { data: 'total', name: 'total' },
                    { data: 'payment_method', name: 'payment_method' },
                    { data: 'transaction_date', name: 'transaction_date' }
                ],
                language: {
                    processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
                    emptyTable: '<div class="text-center p-4"><i class="fas fa-box-open fa-3x text-muted"></i><p class="mt-2">No data available</p></div>',
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    search: '<i class="fas fa-search"></i>',
                    searchPlaceholder: "Search POS reports..."
                },
                pageLength: 10,
                order: [[1, 'desc']]
            });
        });
    </script>
@endpush
