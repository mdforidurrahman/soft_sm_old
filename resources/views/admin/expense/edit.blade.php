{{-- Edit Expense Modal --}}
<div class="modal fade" id="editExpenseModal" tabindex="-1" aria-labelledby="editExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editExpenseModalLabel">
                    <i class="fas fa-money-bill-wave"></i> Edit Expense
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editExpenseForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editExpenseId" name="id">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_store_id" class="form-label">
                                <i class="fas fa-store"></i> Store Name*
                            </label>
                            <select name="store_id" class="form-select" id="edit_store_id" required>
                                <option value="">Please Select</option>
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}">{{ $store->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_expense_category_id" class="form-label">
                                <i class="fas fa-list"></i> Expense Category*
                            </label>
                            <select name="expense_category_id" class="form-select" id="edit_expense_category_id" required>
                                <option value="">Please Select</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_reference_no" class="form-label">
                                <i class="fas fa-hashtag"></i> Reference No
                            </label>
                            <input type="text" class="form-control" id="edit_reference_no" name="reference_no">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_expense_date" class="form-label">
                                <i class="fas fa-calendar-alt"></i> Date*
                            </label>
                            <input type="date" class="form-control" id="edit_expense_date" name="expense_date" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_expense_for_id" class="form-label">
                                <i class="fas fa-user"></i> Expense For
                            </label>
                            <select name="expense_for_id" class="form-select" id="edit_expense_for_id">
                                <option value="">None</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_expense_for_contact" class="form-label">
                                <i class="fas fa-address-book"></i> Expense For Contact
                            </label>
                            <select name="expense_for_contact" class="form-select" id="edit_expense_for_contact">
                                <option value="">None</option>
                                @foreach($contacts as $contact)
                                    <option value="{{ $contact->id }}">{{ $contact->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_document" class="form-label">
                            <i class="fas fa-file-upload"></i> Attach Document
                        </label>
                        <input type="file" class="form-control" id="edit_document" name="document" accept=".pdf,.csv,.zip,.doc,.docx,.jpeg,.jpg,.png">
                        <div id="edit-document-preview" class="mt-3"></div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_total_amount" class="form-label">
                                <i class="fas fa-dollar-sign"></i> Total Amount*
                            </label>
                            <input type="number" class="form-control" id="edit_total_amount" name="total_amount" required>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="updateExpense()">
                    <i class="fas fa-save"></i> Update Expense
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Function to open the edit modal and fetch expense details
    function openEditModal(url) {
        showLoader();
        $.ajax({
            url: url,
            method: 'GET',
            success: function (response) {
                populateEditModal(response);
                $('#editExpenseModal').modal('show');
                hideLoader();
            },
            error: function (xhr) {
                alert('Failed to fetch expense details. Please try again.');
                hideLoader();
            }
        });
    }

    // Function to populate the edit modal with data
    function populateEditModal(response) {
        $('#editExpenseId').val(response.id);
        $('#edit_store_id').val(response.store.id);
        $('#edit_expense_category_id').val(response.expense_category.id);
        $('#edit_reference_no').val(response.reference_no);
        $('#edit_expense_date').val(response.expense_date);
        $('#edit_expense_for_id').val(response.user.id);
        $('#edit_expense_for_contact').val(response.contact ? response.contact.id : '');
        $('#edit_total_amount').val(response.total_amount);

        if (response.document) {
            $('#edit-document-preview').html(`<a href="/storage/${response.document}" target="_blank">View Document</a>`);
        } else {
            $('#edit-document-preview').html('');
        }
    }

    // Function to update the expense
    function updateExpense() {
        showLoader();

        const expenseId = $('#editExpenseId').val();
        const updateUrl = "{{ route($role . 'expense.updateData', ':id') }}".replace(':id', expenseId);
        const formData = new FormData($('#editExpenseForm')[0]);

        $.ajax({
            url: updateUrl,
            method: 'PUT',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                $('#editExpenseModal').modal('hide');
                loadTable(); // Assuming this function reloads the expense table
                toastr.success('Expense updated successfully!');
                hideLoader();
            },
            error: function (xhr) {
                let errorMessage = 'An error occurred while updating the expense.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                }
                toastr.error(errorMessage);
                hideLoader();
            }
        });
    }
</script>
