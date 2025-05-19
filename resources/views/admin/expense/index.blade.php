@extends('layouts.admin')
@section('title', 'Expense List')

@push('style')
    @include('import.css.datatable')
@endpush

@section('content')
    <x-breadcumb title="Expense List"/>

    <div class="table-responsive">
        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#invoiceModal"></button>

        <div class="dashboard-card">
            <div class="card-header-section">
                <div class="table-title-section">
                    <div class="table-icon">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <h5 class="table-title">Expense Overview</h5>
                </div>
                <div class="header-actions">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#expenseModal">
                        Add New Expense
                    </button>
                </div>
            </div>
            <div class="table-responsive">
                <table id="example2" class="table table-hover">
                    <thead>
                    <tr>
                        <th>SL</th>
                        <th>Invoice</th>
                        <th>Action</th>
                        <th>Status</th>

                        <th>Store Name</th>
                        <th>Reference no</th>
                        <th>Expense date</th>
                        <th>Expense for id</th>
                        <th>Expense for contact</th>
                        <th>Expense Category</th>
                        <th>Document</th>
                        <th>Total Amount</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Invoice Modal -->
    <div class="modal fade" id="invoiceModal" tabindex="-1" aria-labelledby="invoiceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="invoiceModalLabel">Invoice Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="invoice-details-content"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="printInvoice()">Print Invoice</button>
                </div>
            </div>
        </div>
    </div>

    {{-- <!-- Include Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet"> --}}

@endsection

@push('script')
    @include('import.js.datatable')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>


    @include('admin.expense.create', ['stores' => $stores, 'users' => $users, 'contacts' => $contacts])
    <script>
        loadTable();

        function loadTable() {

            const columns =  [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'invoice',
                    name: 'invoice',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return `
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton-${row.id}" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-file-invoice"></i>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton-${row.id}">
                                        <li><a class="dropdown-item" href="#" onclick="showInvoice(${row.id})">Show Invoice</a></li>
                                         <li><a class="dropdown-item" href="#" onclick="downloadInvoice(${row.id})">Download Invoice</a></li>
                                    </ul>
                                </div>`;
                    }
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'store_id',
                    name: 'store_id'
                },
                {
                    data: 'reference_no',
                    name: 'reference_no'
                },
                {
                    data: 'expense_date',
                    name: 'expense_date'
                },
                {
                    data: 'expense_for_id',
                    name: 'expense_for_id'
                },
                {
                    data: 'expense_for_contact',
                    name: 'expense_for_contact'
                },
                {
                    data: 'expense_category_id',
                    name: 'expense_category_id'
                },
                {
                    data: 'document',
                    name: 'document',
                    render: function (data) {
                        return data ?
                            `<a href="{{ url('storage') }}/${data}" target="_blank">View Document</a>` :
                            'No Document';
                    },
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'total_amount',
                    name: 'total_amount'
                },


            ];
            initDataTable
            (
                '#example2',
                '{{ route($role . 'expense.index') }}',
                columns
            )
        }

    </script>
    <script>

        function showInvoice(id) {
            $.ajax({
                url: `/{{ rtrim($role, '.') }}/expense/${id}/show-invoice`,
                type: 'GET',
                success: function (data) {
                    $('#invoice-details-content').html(data);
                    $('#invoiceModal').modal('show');
                },
                error: function () {
                    alert('Failed to load invoice details.');
                }
            });
        }


        function printInvoice() {
            var printContents = document.querySelector('#invoice-details-content').innerHTML;
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            window.location.reload();
        }

        {{--function downloadInvoice(id) {--}}
        {{--    // Fetch the invoice details using AJAX--}}
        {{--    $.ajax({--}}
        {{--        url: `/{{ rtrim($role, '.') }}/expense/${id}/show-invoice`,--}}
        {{--        type: 'GET',--}}
        {{--        success: function (data) {--}}
        {{--            const tempDiv = document.createElement('div');--}}
        {{--            tempDiv.innerHTML = data;--}}
        {{--            tempDiv.style.color = 'black'; // Ensure text color is set for PDF--}}
        {{--            tempDiv.style.fontSize = '22px';--}}
        {{--            tempDiv.style.padding = '10px';--}}
        {{--            tempDiv.style.height = '100vh';--}}
        {{--            tempDiv.style.margin = 'auto';--}}
        {{--            tempDiv.style.border = '1px solid black';--}}
        {{--            document.body.appendChild(tempDiv); // Temporarily add to document to render styles--}}

        {{--            // Use jsPDF to generate the PDF--}}
        {{--            const {--}}
        {{--                jsPDF--}}
        {{--            } = window.jspdf;--}}
        {{--            const pdf = new jsPDF('p', 'mm', 'a4');--}}

        {{--            // Set options for html2canvas to capture the styles correctly--}}
        {{--            pdf.html(tempDiv, {--}}
        {{--                callback: function (pdf) {--}}
        {{--                    document.body.removeChild(tempDiv); // Remove the temp div--}}
        {{--                    pdf.save(`invoice_${id}.pdf`);--}}
        {{--                },--}}
        {{--                x: 10,--}}
        {{--                y: 10,--}}
        {{--                html2canvas: {--}}
        {{--                    scale: 0.13, // Adjust scaling to ensure content fits well--}}
        {{--                    logging: false,--}}
        {{--                    useCORS: true, // Allow loading of external resources--}}
        {{--                    windowHeight: document.body.scrollHeight,--}}
        {{--                    windowWidth: document.body.scrollWidth--}}
        {{--                }--}}
        {{--            });--}}
        {{--        },--}}
        {{--        error: function () {--}}
        {{--            alert('Failed to download invoice.');--}}
        {{--        }--}}
        {{--    });--}}
        {{--}--}}

        function printInvoice() {
            var printContents = document.querySelector('#invoice-details-content').innerHTML;
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            window.location.reload();
        }
    </script>

    @include('admin.expense.edit')
@endpush
