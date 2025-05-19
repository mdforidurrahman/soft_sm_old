{{-- Ledger Modal --}}
@push('style')
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

	<link rel="stylesheet"
	      href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.min.css">

	<style>
        .modal-content {
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            background: #4338ca;
            color: white;
            border-radius: 12px 12px 0 0;
            padding: 1rem 1.5rem;
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: white;
        }

        .btn-close {
            color: white;
            opacity: 1;
        }

        .customer-info,
        .account-summary {
            background: #f3f4f6;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        .customer-info h6,
        .account-summary h6 {
            color: #4338ca;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .form-control {
            border-radius: 6px;
            border: 1px solid #e5e7eb;
            padding: 0.5rem 1rem;
        }

        .form-control:focus {
            border-color: #4338ca;
            box-shadow: 0 0 0 2px rgba(67, 56, 202, 0.1);
        }

        .table {
            border-radius: 8px;
            overflow: hidden;
        }

        .table thead th {
            background: #4338ca;
            color: white;
            font-weight: 500;
            border: none;
            padding: 1rem;
        }

        .table tbody td {
            padding: 1rem;
            border-color: #e5e7eb;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f9fafb;
        }

        .btn-primary {
            background: #4338ca;
            border: none;
            border-radius: 6px;
            padding: 0.5rem 1.5rem;
        }

        .btn-primary:hover {
            background: #3730a3;
        }

        .btn-secondary {
            background: #9ca3af;
            border: none;
            border-radius: 6px;
            padding: 0.5rem 1.5rem;
        }

        .status-paid {
            color: #059669;
            background: #d1fae5;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
        }

        .status-pending {
            color: #d97706;
            background: #fef3c7;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
        }

        .summary-card {
            background: white;
            border-radius: 8px;
            padding: 1rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .summary-value {
            font-size: 1.25rem;
            font-weight: 600;
            color: #4338ca;
        }
	</style>
@endpush

<div class="modal fade" id="openLedgerModal" tabindex="-1" aria-labelledby="openLedgerModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="ledgerModalLabel">Customer Ledger</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<input type="hidden" id="customerId" value="">

				<div class="row mb-4">
					<div class="col-md-4">
						<div class="form-group">
							<label for="ledgerDateRange" class="mb-2">Date Range:</label>
							<div class="input-group">
								<span class="input-group-text"><i class="fas fa-calendar"></i></span>
								<input type="text" class="form-control" id="ledgerDateRange" name="daterange">
							</div>
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group">
							<label for="businessLocation" class="mb-2">Business Location:</label>
							<select class="form-control" id="businessLocation">
								<option value="all">All Locations</option>
							</select>
						</div>
					</div>
				</div> 

				<div class="customer-info mb-4">
					<h6>Customer Details</h6>
					<div id="customerDetails" class="row">
						<!-- Customer details will be populated here -->
                      
					</div>
               
				</div>


              
				<div class="customer-info mb-4">
					<h6>Customer Image</h6>
					<div class="text-center">
						<img id="customerImageDisplay" src="" alt="Customer Image"
						     class="img-thumbnail" style="max-width: 200px; display: none;">
						<p id="noImageText" class="text-muted mt-2" style="display: none;">No image available</p>
					</div>
				</div>

				<div class="account-summary mb-4">
					<h6>Account Summary</h6>
					<div class="row">
						<div class="col-md-6">
							<div class="summary-card mb-3">
								<!--<p class="mb-2">Period: <span id="summaryPeriod" class="summary-value"></span></p> -->
								<p class="mb-2">Total Invoice: <span id="totalInvoice" class="summary-value"></span></p>
								<p class="mb-0">Total Paid: <span id="totalPaid" class="summary-value"></span></p>
							</div>
						</div>
						<div class="col-md-6">
							<div class="summary-card">
								<h6 class="mb-3">Overall Summary</h6>
								<p class="mb-2">Total Invoice: <span id="overallInvoice" class="summary-value"></span>
								</p>
								<p class="mb-2">Total Paid: <span id="overallPaid" class="summary-value"></span></p>
								<p class="mb-0">Balance Due: <span id="balanceDue" class="summary-value"></span></p>
							</div>
						</div>
					</div>
				</div>

				<div class="table-responsive">
					<table class="table table-bordered table-striped">
						<thead>
						<tr>
							<th>Date</th>
							<th>Reference No</th>
							<th>Type</th>
							<th>Location</th>
							<th>Payment Status</th>
							<th>Debit</th>
							<th>Credit</th>
							<th>Payment Method</th>
							<th>Others</th>
						</tr>
						</thead>
						<tbody id="ledgerTableBody">
						</tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
				<button type="button" id="downloadPdfBtn" class="btn btn-primary">
					<i class="fas fa-download"></i> Download PDF
				</button>
			</div>
		</div>
	</div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.min.js"></script>
<script>
    // Initialize date range picker with fallback
    $(document).ready(function () {
        try {
            $('#ledgerDateRange').daterangepicker({
                startDate: moment().startOf('year'),
                endDate: moment().endOf('year'),
                locale: {
                    format: 'MM/DD/YYYY'
                }
            });
        } catch (e) {
            console.error('Error initializing daterangepicker:', e);
            // Fallback to simple date input if daterangepicker fails
            $('#ledgerDateRange').attr('type', 'date');
        }

        $('#downloadPdfBtn').click(function () {
            const customerId = $('#customerId').val();
            const dateRange = $('#ledgerDateRange').val();
            const storeId = $('#businessLocation').val();

            if (!customerId) {
                alert('Please select a customer first');
                return;
            }

            // Show loading indicator
            // $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Downloading...');

            $.ajax({
                url: `/customer/ledger/${customerId}/download`,
                method: 'GET',
                data: {
                    date_range: dateRange,
                    store_id: storeId
                },
                xhrFields: {
                    responseType: 'blob'
                },
                success: function (data, status, xhr) {
                    $('#downloadPdfBtn').prop('disabled', false).html('<i class="fas fa-download"></i> Download PDF');
                    console.log('Success Response:', {
                        data: data,
                        status: status,
                        contentType: xhr.getResponseHeader('Content-Type')
                    });

                    // Check if response is a blob

                    // Verify blob type
                    if (data.type !== 'application/pdf') {
                        console.error('Blob is not a PDF:', data.type);
                        alert('Failed to generate PDF. Invalid file type.');
                        return;
                    }

                    // Get filename from Content-Disposition header
                    const filename = xhr.getResponseHeader('Content-Disposition')
                        ?.split('filename=')[1]
                        ?.replace(/"/g, '') || `customer_ledger.pdf`;

                    // Create a blob URL and trigger download
                    const blob = new Blob([data], {type: 'application/pdf'});
                    const link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = filename;
                    link.click();
                },
                error: function (xhr, status, error) {
                    console.error('PDF Download Error:', {
                        status: status,
                        error: error,
                        responseText: xhr.responseText,
                        responseType: xhr.responseType
                    });
                    $('#downloadPdfBtn').prop('disabled', false).html('<i class="fas fa-download"></i> Download PDF');
                    try {
                        // Try to parse error response
                        const errorResponse = JSON.parse(xhr.responseText);
                        alert(errorResponse.message || 'Failed to download PDF. Please try again.');
                    } catch (parseError) {
                        // If parsing fails, show generic error
                        alert('Failed to download PDF. Server error occurred.');
                    }
                },
                complete: function () {
                    // Re-enable button
                    $('#downloadPdfBtn').prop('disabled', false).html('<i class="fas fa-download"></i> Download PDF');
                }
            });
        });
    });

    // Function to safely format date
    function safeDateFormat(date) {
        try {
            return moment(date).format('MM/DD/YYYY HH:mm');
        } catch (e) {
            console.error('Error formatting date:', e);
            return new Date(date).toLocaleString();
        }
    }

    // Function to safely format currency
    function formatCurrency(amount) {
        try {
            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD'
            }).format(amount || 0);
        } catch (e) {
            console.error('Error formatting currency:', e);
            return `$${(amount || 0).toFixed(2)}`;
        }
    }

    // Function to open ledger modal
    function viewLedgerModal(route) {
        if (!route) {
            console.error('No route provided to openViewModal');
            return;
        }
        $('#openLedgerModal').modal('show');
        loadLedgerData(route);
    }

    // Function to load ledger data with error handling


    function loadLedgerData(route) {
        showLoader();
        const dateRange = $('#ledgerDateRange').val();
        const format = $('.btn-group button.active').data('format') || 1;
        const store_id = $('#businessLocation').val(); // Changed from location to store_id

        $.ajax({
            url: route,
            method: 'GET',
            data: {
                date_range: dateRange,
                format: format,
                store_id: store_id
            },
            success: function (response) {
                if (!response) {
                    console.error('Empty response received');
                    return;
                }

                // Update the business locations dropdown if stores data is present
                if (response.stores) {
                    updateBusinessLocations(response.stores);
                }

                updateCustomerDetails(response.customer);
                updateAccountSummary(response.summary);
                updateLedgerTable(response.transactions);
            },
            error: function (xhr, status, error) {
                console.error('Error loading ledger data:', error);
                alert('Error loading ledger data. Please try again.');
            },
            complete: function () {
                hideLoader();
            }
        });
    }


    function updateBusinessLocations(stores) {
        if (!Array.isArray(stores)) {
            console.error('Invalid stores data:', stores);
            return;
        }

        const locationSelect = $('#businessLocation');
        // Keep the "All Locations" option and remove others
        locationSelect.find('option:not([value="all"])').remove();

        // Add new store options
        stores.forEach(store => {
            if (!store || !store.id || !store.name) return;

            locationSelect.append(`
            <option value="${store.id}">${store.name}</option>
        `);
        });

        // Maintain selected value if exists
        const currentValue = locationSelect.data('current-value');
        if (currentValue) {
            locationSelect.val(currentValue);
        }
    }


    // Function to update customer details with sanitization
    function updateCustomerDetails(customer) {
        if (!customer) return;

        const sanitizeHtml = (str) => {
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        };

        $('#customerId').val(customer.id);

        $('#customerDetails').html(`
        <p><strong>Name:</strong> ${sanitizeHtml(customer.name || '-')}</p>
        <p><strong>District:</strong> ${sanitizeHtml(customer.district || '-')}</p>
        <p><strong>Thana:</strong> ${sanitizeHtml(customer.thana || '-')}</p>
        <p><strong>Post Office:</strong> ${sanitizeHtml(customer.post_office || '-')}</p>
        <p><strong>Village:</strong> ${sanitizeHtml(customer.village || '-')}</p>
        <p><strong>Mobile:</strong> ${sanitizeHtml(customer.phone || '-')}</p>
    `);

        // Handle customer image display
        const imageDisplay = $('#customerImageDisplay');
        const noImageText = $('#noImageText');

        if (customer.image) {
            // If image exists, show it and hide the "no image" text
            imageDisplay.attr('src', window.location.origin + '/' + customer.image).show();
            noImageText.hide();
        } else {
            // If no image, hide the image element and show the text
            imageDisplay.hide();
            noImageText.show();
        }
    }

    // Function to update account summary with error handling
    function updateAccountSummary(summary) {
        if (!summary) return;

        $('#summaryPeriod').text(summary.period || '-');
        $('#totalInvoice').text(formatCurrency(summary.total_invoice));
        $('#totalPaid').text(formatCurrency(summary.total_paid));
        $('#overallInvoice').text(formatCurrency(summary.overall_invoice));
        $('#overallPaid').text(formatCurrency(summary.overall_paid));
        $('#balanceDue').text(formatCurrency(summary.balance_due));
    }

    $('#businessLocation').change(function () {
        const selectedStoreId = $(this).val();
        $(this).data('current-value', selectedStoreId); // Save current selection

        const customerId = $('#customerId').val();

        console.log('Selected store ID:', selectedStoreId);

        console.log('Customer ID:', customerId);


        if (customerId) {
            loadLedgerData(`/customer/ledger/${customerId}`);
        }
    });


    // Function to update ledger table with error handling
    function updateLedgerTable(transactions) {
        if (!Array.isArray(transactions)) {
            console.error('Invalid transactions data:', transactions);
            return;
        }

        const tbody = $('#ledgerTableBody');
        tbody.empty();

        transactions.forEach(trans => {
            if (!trans) return;

            tbody.append(`
                <tr>
                    <td>${safeDateFormat(trans.date)}</td>
                    <td>${trans.reference_no || '-'}</td>
                    <td>${trans.type || '-'}</td>
                    <td>${trans.location || '-'}</td>
                    <td>${trans.payment_status || '-'}</td>
                    <td>${formatCurrency(trans.debit)}</td>
                    <td>${formatCurrency(trans.credit)}</td>
                    <td>${trans.payment_method || '-'}</td>
                    <td>${trans.others || '-'}</td>
                </tr>
            `);
        });
    }
    // Function to print ledger
    function printLedger() {
        window.print();
    }

    // Event listeners
    $('.btn-group button').click(function (e) {
		e.preventDefault();
        $('.btn-group button').removeClass('active');
        $(this).addClass('active');
        const customerId = $('#customerId').val();
        if (customerId) {
            loadLedgerData(customerId);
        }
    });

    $('#ledgerDateRange, #businessLocation').change(function () {
        const customerId = $('#customerId').val();
        if (customerId) {
            loadLedgerData(`/customer/ledger/${customerId}`);
        }
    });


</script>