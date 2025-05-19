@extends('layouts.admin')
@section('title', 'Sell List')

@push('style')
    @include('import.css.datatable')
@endpush

@section('content')
    <x-breadcumb title="Sell List"/>
    <div class="table-responsive">


        <div class="dashboard-card">
            <div class="card-header-section">
                <div class="table-title-section">
                    <div class="table-icon">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <h5 class="table-title">Sell Overview</h5>
                </div>
                <div class="header-actions">
                    <button id="add_new_sell" type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#sellModal">
                        Add New sell
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
                        <th>Customer Name</th>
                        <th>sell Date</th>
                        <th>Net Total</th>
                        <th>Discount Amount</th>
                        <th>Tax Amount</th>
                        <th>Payment Due</th>
                        <th>Refrence No</th>
                        <th>invoice No</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
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
@endsection

@push('script')
    @include('import.js.datatable')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>



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
    			data: 'customer_name',
    			name: 'customer_name',                    
                },
                {
                    data: 'sell_date',
                    name: 'sell_date'
                },
                {
                    data: 'net_total',
                    name: 'net_total'
                },
                {
                    data: 'discount_amount',
                    name: 'discount_amount'
                },
                {
                    data: 'tax_amount',
                    name: 'tax_amount',
                },
                {
                    data: 'payment_due',
                    name: 'payment_due',
                },
                {
                    data: 'reference_no',
                    name: 'reference_no'
                },
                {
                    data: 'invoice_no',
                    name: 'invoice_no'
                },
                {
                    data: 'sell_status',
                    name: 'sell_status'
                },

            ];

            initDataTable
            (
                '#example2',
                '{{ route($role . 'sell.index') }}',
                columns
            );
        }

        function showInvoice(id) {
            $.ajax({
                url: `/{{ rtrim($role, '.') }}/sell/${id}/show-invoice`,
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

        function downloadInvoice(id) {
            $.ajax({
                url: `/{{ rtrim($role, '.') }}/sell/${id}/show-invoice`,
                type: 'GET',
                success: function (data) {
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = data;
                    tempDiv.style.color = 'black'; // Ensure text color is set for PDF
                    tempDiv.style.fontSize = '22px';
                    tempDiv.style.padding = '10px';
                    tempDiv.style.height = '100vh';
                    tempDiv.style.margin = 'auto';
                    tempDiv.style.border = '1px solid black';
                    document.body.appendChild(tempDiv);

                    // Use jsPDF to generate the PDF
                    const {
                        jsPDF
                    } = window.jspdf;
                    const pdf = new jsPDF('p', 'mm', 'a4');

                    // Set options for html2canvas to capture the styles correctly
                    pdf.html(tempDiv, {
                        callback: function (pdf) {
                            document.body.removeChild(tempDiv); // Remove the temp div
                            pdf.save(`invoice_${id}.pdf`);
                        },
                        x: 10,
                        y: 10,
                        html2canvas: {
                            scale: 0.10, // Adjust scaling to ensure content fits well
                            logging: false,
                            useCORS: true, // Allow loading of external resources
                            windowHeight: document.body.scrollHeight,
                            windowWidth: document.body.scrollWidth
                        }
                    });
                },
                error: function () {
                    alert('Failed to download invoice.');
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
    </script>
    @include('admin.sell.create')

    @include('admin.sell.return-sell')

    @include('admin.sell.edit')
@endpush