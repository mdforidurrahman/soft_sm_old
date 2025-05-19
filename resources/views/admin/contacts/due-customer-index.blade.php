@extends('layouts.admin')
@section('title', 'Due Customers List')

@push('style')
@include('import.css.datatable')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.min.js"></script>
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.min.css">
@endpush

@section('content')
<x-breadcumb title="Due Customers List" />
<div class="table-responsive">
    <div class="dashboard-card">
        <div class="card-header-section">
            <div class="table-title-section">
                <div class="table-icon">
                    <i class="fas fa-project-diagram"></i>
                </div>
                <h5 class="table-title">Due Customers Overview</h5>
            </div>
            <div class="header-actions">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#contactModal">
                    Add New Contacts
                </button>
            </div>
        </div>
        <div class="table-responsive">
            <table id="example2" class="table table-hover">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Action</th>
                        <th>Status</th>
                        <th>Contact Id</th>
                        <th>Name</th>
                        <th>Father Name</th>
                        <th>Role</th>
                        <th>Sales Type</th>
                        <th>Phone</th>
                        <th>Installment Amount (TK)</th>
                        <th>Sale Date</th>
                        <th>Payment Term</th>
                        <th>Total Sales</th>
                        <th>Total Paid</th>
                        <th>Total Due</th>
                        <th>NID</th>
                        <th>District</th>
                        <th>Thana</th>
                        <th>Post Office</th>
                        <th>Village</th>
                        <th>Media/Grander Name (if have)</th>
                        <th>Media/Grander Phone Number (if have)</th>
                        <th>Customer Ledger Image</th>
                        <th>Customer NID Picture</th>
                        <th>Customer Image</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="12"></th>
                        <th>Total Net Total</th>
                        <th>Total Discount</th>
                        <th>Total Payment Due</th>
                        <th colspan="10"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection

@push('script')
@include('import.js.datatable')
@include('admin.contacts.create')
<script>
    loadTable();

    function loadTable() {
        const columns = [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false },
            { data: 'status', name: 'status' },
            { data: 'contact_id', name: 'contact_id' },
            { data: 'name', name: 'name' },
            { data: 'father_name', name: 'father_name' },
            { data: 'role', name: 'role' },
            { data: 'sales_type', name: 'sales_type' },
            { data: 'phone', name: 'phone' },
            { data: 'installment', name: 'installment' },
            {
                data: 'sale_date',
                name: 'sale_date',
                render: function(data) {
                    return data ? moment(data).format('YYYY-MM-DD') : 'No sales';
                }
            },
            {
                data: 'payment_term',
                name: 'payment_term',
                render: function(data) {
                    return data ? data + ' Month' : 'N/A';
                }
            },
            {
                data: 'total_invoice',
                name: 'total_invoice',
                render: function(data) {
                    return data ? data : '0.00 TK';
                }
            },
            {
                data: 'total_paid',
                name: 'total_paid',
                render: function(data) {
                    return data ? data : '0.00 TK';
                }
            },
            {
                data: 'balance_due',
                name: 'balance_due',
                render: function(data) {
                    return data ? data : '0.00 TK';
                }
            },
            { data: 'nid', name: 'nid' },
            { data: 'district', name: 'district' },
            { data: 'thana', name: 'thana' },
            { data: 'post_office', name: 'post_office' },
            { data: 'village', name: 'village' },
            { data: 'media_name', name: 'media_name' },
            { data: 'media_number', name: 'media_number' },
            { data: 'image', name: 'image' },
            { data: 'finger_print', name: 'finger_print' },
            { data: 'signature', name: 'signature' }
        ];

        initDataTable(
            '#example2',
            '{{ route($role . "customers.due") }}',
            columns,
            {
                order: [[3, 'desc']],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();

                    // Helper function to remove formatting and parse numbers (strip " TK")
                    var intVal = function(i) {
                        if (typeof i === 'string') {
                            return parseFloat(i.replace(/[^0-9.-]+/g,"")) || 0;
                        }
                        return typeof i === 'number' ? i : 0;
                    };

                    var totalNetTotal = api
                        .column(12, { page: 'current' })
                        .data()
                        .reduce(function(a, b) { return intVal(a) + intVal(b); }, 0);

                    var totalDiscount = api
                        .column(13, { page: 'current' })
                        .data()
                        .reduce(function(a, b) { return intVal(a) + intVal(b); }, 0);

                    var totalPaymentDue = api
                        .column(14, { page: 'current' })
                        .data()
                        .reduce(function(a, b) { return intVal(a) + intVal(b); }, 0);

                    $(api.column(12).footer()).html(totalNetTotal.toFixed(2) + ' TK');
                    $(api.column(13).footer()).html(totalDiscount.toFixed(2) + ' TK');
                    $(api.column(14).footer()).html(totalPaymentDue.toFixed(2) + ' TK');
                }
            }
        );
    }
</script>
@include('admin.contacts.edit')
@include('admin.contacts.showCustomerLedger')
@include('admin.contacts.customer-ledger-pay')
@endpush