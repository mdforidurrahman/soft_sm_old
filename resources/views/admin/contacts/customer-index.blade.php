@extends('layouts.admin')
@section('title', 'Contacts List')

@push('style')
    @include('import.css.datatable')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.min.js"></script>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.min.css">
@endpush

@section('content')
    <x-breadcumb title="Customers List" />
    <div class="table-responsive">
        <div class="dashboard-card">
            <div class="card-header-section">
                <div class="table-title-section">
                    <div class="table-icon">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <h5 class="table-title">Customers list Overview</h5>
                </div>
                <div class="header-actions">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#contactModal">
                        Add New Contacts
                    </button>
                </div>
            </div>
            <div class="table-responsive">
                <table id="example2" class="table table-hover" style="width: 100%;height: 300px;">
                    <thead>
                        <tr>

                            <th>SL</th>
                            <th>Action</th>
                            <th>Status</th>
                            <th>Contact Id</th>
                            <th>Name</th>
                            <th>Father Name</th>                            
                            <th>Installment (TK)</th>
                            <th>Total Invoice (TK)</th>
            				<th>Total Paid (TK)</th>
            				<th>Balance Due (TK)</th>
                            <!-- <th>Role</th> -->
                            <th>Sales Type</th>
                            <th>Phone</th>
                            <th>NID No.</th>
                            <th>District</th>
                            <th>Thana</th>
                            <th>Post Office</th>
                            <th>Village</th>
                          	<th>Media/Grander Name</th>
                            <th>Media/Grander Mobile No.</th>
                            <th>Customer Ledger Image</th>
                            <th>NID Picture</th>
                            <th>Customer Image</th>
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

    @include('admin.contacts.create')
    <script>
        loadTable();

        function loadTable() {

            const columns = [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }, {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'contact_id',
                    name: 'contact_id'
                },
                {
                    data: 'name',
                    name: 'name'
                }, {
                    data: 'father_name',
                    name: 'father_name'
                },
                {
                    data: 'installment',
                    name: 'installment'
                }, {
                    data: 'total_invoice',
                    name: 'total_invoice'
                }, {
                    data: 'total_paid',
                    name: 'total_paid'
                }, {
                    data: 'balance_due',
                    name: 'balance_due'
                },
                {
                    data: 'sales_type',
                    name: 'sales_type'
                },
                {
                    data: 'phone',
                    name: 'phone'
                },
                {
                    data: 'nid',
                    name: 'nid'
                },
                {
                    data: 'district',
                    name: 'district'
                },
                {
                    data: 'thana',
                    name: 'thana'
                },
                {
                    data: 'post_office',
                    name: 'post_office'
                },
                {
                    data: 'village',
                    name: 'village'
                },
                {
                    data: 'media_name',
                    name: 'media_name'
                },
                {
                    data: 'media_number',
                    name: 'media_number'
                },
                {
                    data: 'image',
                    name: 'image'
                },
                {
                    data: 'finger_print',
                    name: 'finger_print'
                },
                {
                    data: 'signature',
                    name: 'signature'
                },
            ];
            initDataTable(
                '#example2',
                '{{ route($role . 'customers.index') }}',
                columns
    );
}
    </script>

    @include('admin.contacts.edit')
    @include('admin.contacts.showCustomerLedger')
    @include('admin.contacts.customer-ledger-pay')
@endpush