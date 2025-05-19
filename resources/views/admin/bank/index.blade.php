@extends('layouts.admin')
@section('title', 'Bank List')

@push('style')
	@include('import.css.datatable')
@endpush

@section('content')
	<x-breadcumb title="Bank List"/>
	<div class="table-responsive">
		<div class="dashboard-card">
			<div class="card-header-section">
				<div class="table-title-section">
					<div class="table-icon">
						<i class="fas fa-university"></i>
					</div>
					<h5 class="table-title">Bank Accounts Overview</h5>
				</div>
				<div class="header-actions">
					<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bankModal">
						Add New Bank Account
					</button>
				</div>
			</div>
			<div class="table-responsive">
				<table id="example2" class="table table-hover">
					<thead>
					<tr>
						<th>SL</th>
						<th>Store</th>
						<th>Bank Name</th>
						<th>Account Holder</th>
						<th>Account Number</th>
						<th>Balance</th>
						<th>Action</th>
					</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	{{-- Add Modal --}}
	<div class="modal fade" id="bankModal" tabindex="-1" aria-labelledby="bankModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="bankModalLabel">Add New Bank Account</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<form id="bankForm">
						@csrf
						<div class="mb-3">
							<label for="store_id" class="form-label">Store</label>
							<select class="form-control" id="store_id" name="store_id" required>
								<option value="">Select Store</option>
								@foreach($stores as $store)
									<option value="{{ $store->id }}">{{ $store->name }}</option>
								@endforeach
							</select>
						</div>
						<div class="mb-3">
							<label for="bank_name" class="form-label">Bank Name</label>
							<input type="text" class="form-control" id="bank_name" name="bank_name" required>
						</div>
						<div class="mb-3">
							<label for="account_holder_name" class="form-label">Account Holder Name</label>
							<input type="text" class="form-control" id="account_holder_name" name="account_holder_name" required>
						</div>
						<div class="mb-3">
							<label for="account_number" class="form-label">Account Number</label>
							<input type="text" class="form-control" id="account_number" name="account_number" required>
						</div>
						<div class="mb-3">
							<label for="current_balance" class="form-label">Initial Balance</label>
							<input type="number" step="0.01" class="form-control" id="current_balance" name="current_balance" value="0.00" required>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary" onclick="submitForm()">Save Account</button>
				</div>
			</div>
		</div>
	</div>

	{{-- Edit Modal --}}
	<div class="modal fade" id="editBankModal" tabindex="-1" aria-labelledby="editBankModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="editBankModalLabel">Edit Bank Account</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<form id="editBankForm">
						@csrf
						@method('PUT')
						<input type="hidden" id="editBankId" name="id">
						<div class="mb-3">
							<label for="editStoreId" class="form-label">Store</label>
							<select class="form-control" id="editStoreId" name="store_id" required>
								@foreach($stores as $store)
									<option value="{{ $store->id }}">{{ $store->name }}</option>
								@endforeach
							</select>
						</div>
						<div class="mb-3">
							<label for="editBankName" class="form-label">Bank Name</label>
							<input type="text" class="form-control" id="editBankName" name="bank_name" required>
						</div>
						<div class="mb-3">
							<label for="editAccountHolder" class="form-label">Account Holder Name</label>
							<input type="text" class="form-control" id="editAccountHolder" name="account_holder_name" required>
						</div>
						<div class="mb-3">
							<label for="editAccountNumber" class="form-label">Account Number</label>
							<input type="text" class="form-control" id="editAccountNumber" name="account_number" required>
						</div>
						<div class="mb-3">
							<label for="editCurrentBalance" class="form-label">Current Balance</label>
							<input type="number" step="0.01" class="form-control" id="editCurrentBalance" name="current_balance" required>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary" onclick="updateBank()">Update Account</button>
				</div>
			</div>
		</div>
	</div>
@endsection

@push('script')
	@include('import.js.datatable')

	<script>
        function submitForm() {
            $.ajax({
                url: "{{ route($role . 'banks.store') }}",
                method: 'POST',
                data: $('#bankForm').serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    $('#bankModal').modal('hide');
                    $('#bankForm')[0].reset();
                    loadTable();
                    AjaxNotifications.handle(response);
                },
                error: function (xhr) {
                    let response = JSON.parse(xhr.responseText);
                    if (xhr.status === 422) {
                        let errorMessages = [];
                        for (let field in response.errors) {
                            errorMessages = errorMessages.concat(response.errors[field]);
                        }
                        AjaxNotifications.error(errorMessages.join('<br>'));
                    } else {
                        AjaxNotifications.error(response.message || 'An error occurred');
                    }
                    console.error('Error adding bank account:', response);
                }
            });
        }

        $('#bankForm').on('submit', function (e) {
            e.preventDefault();
            submitForm();
        });

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
                    data: 'store',
                    name: 'store',
                },
                {
                    data: 'bank_name',
                    name: 'bank_name',
                },
                {
                    data: 'account_holder_name',
                    name: 'account_holder_name',
                },
                {
                    data: 'account_number',
                    name: 'account_number',
                },
                {
                    data: 'current_balance',
                    name: 'current_balance',
                },

                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ];
            initDataTable(
                '#example2',
                '{{ route($role . 'banks.index') }}',
                columns
            );
        }

        function openEditModal(editUrl) {
            showLoader();
            $.ajax({
                url: editUrl,
                method: 'GET',
                success: function (response) {
                    if (response && typeof response === 'object') {
                        $('#editBankId').val(response.id);
                        $('#editStoreId').val(response.store_id);
                        $('#editBankName').val(response.bank_name);
                        $('#editAccountHolder').val(response.account_holder_name);
                        $('#editAccountNumber').val(response.account_number);
                        $('#editCurrentBalance').val(response.current_balance);

                        $('#editBankModal').modal('show');
                    } else {
                        console.error('Invalid response format:', response);
                        alert('Received invalid data from the server.');
                    }
                    hideLoader();
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching bank data:', {
                        status: status,
                        error: error,
                        responseText: xhr.responseText
                    });
                    hideLoader();
                    alert('Error fetching bank data. Please check the console for more information.');
                }
            });
        }

        function updateBank() {
            showLoader();
            const bankId = $('#editBankId').val();
            const updateUrl = "{{ route($role . 'banks.update', ':id') }}".replace(':id', bankId);

            $.ajax({
                url: updateUrl,
                method: 'POST',
                data: $('#editBankForm').serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    $('#editBankModal').modal('hide');
                    AjaxNotifications.handle(response);
                    hideLoader();
                    loadTable();
                },
                error: function (xhr, status, error) {
                    console.error('Error updating bank:', {
                        status: status,
                        error: error,
                        responseText: xhr.responseText
                    });
                    hideLoader();
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        let errorMessage = 'Validation errors:\n';
                        for (let field in xhr.responseJSON.errors) {
                            errorMessage += `${field}: ${xhr.responseJSON.errors[field].join(', ')}\n`;
                        }
                        AjaxNotifications.error(errorMessage);
                    } else {
                        AjaxNotifications.error('Error updating bank. Please try again.');
                    }
                }
            });
        }

	</script>
@endpush