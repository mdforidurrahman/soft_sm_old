{{-- Add Expense Modal --}}
<div class="modal fade" id="expenseModal" tabindex="-1" aria-labelledby="expenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="expenseModalLabel">
                    <i class="fas fa-money-bill-wave"></i> Add New Expense
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="expenseForm" enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="store_id" class="form-label">
                                <i class="fas fa-store"></i> Store Name*
                            </label>
                            <select name="store_id" class="form-select" id="store_id" required>
                                <option value="">Please Select</option>
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}">{{ $store->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="expense_category_id" class="form-label">
                                <i class="fas fa-list"></i> Expense Category*
                            </label>
                            <select name="expense_category_id" class="form-select" id="expense_category_id" required>
                                <option value="">Please Select</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="reference_no" class="form-label">
                                <i class="fas fa-hashtag"></i> Reference No
                            </label>
                            <input type="text" class="form-control" id="reference_no" name="reference_no" placeholder="Leave empty to autogenerate">
                        </div>
                        <div class="col-md-6">
                            <label for="expense_date" class="form-label">
                                <i class="fas fa-calendar-alt"></i> Date*
                            </label>
                            <input type="date" class="form-control" id="expense_date" name="expense_date" required>
                        </div>
                    </div>

                   <!-- <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="expense_for_id" class="form-label">
                                <i class="fas fa-user"></i> Expense For
                            </label>
                            <select name="expense_for_id" class="form-select" id="expense_for_id">
                                <option value="">None</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="expense_for_contact" class="form-label">
                                <i class="fas fa-address-book"></i> Expense For Contact
                            </label>
                            <select name="expense_for_contact" class="form-select" id="expense_for_contact">
                                <option value="">None</option>
                                @foreach($contacts as $contact)
                                    <option value="{{ $contact->id }}">{{ $contact->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div> -->

                    <div class="mb-3">
                        <label for="document" class="form-label">
                            <i class="fas fa-file-upload"></i> Attach Document
                        </label>
                        <input type="file" class="form-control" id="document" name="document" accept=".pdf,.csv,.zip,.doc,.docx,.jpeg,.jpg,.png">
                        <div id="document-preview" class="mt-3"></div> <!-- Container for the document preview -->
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="total_amount" class="form-label">
                                <i class="fas fa-dollar-sign"></i> Total Amount*
                            </label>
                            <input type="number" class="form-control" id="total_amount" name="total_amount" required>
                        </div>

                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="submitExpenseForm()">
                    <i class="fas fa-save"></i> Save Expense
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function submitExpenseForm() {
        var formData = new FormData(document.getElementById('expenseForm'));

        $.ajax({
            url: "{{ route($role . 'expense.saveData') }}",
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#expenseModal').modal('hide');
                $('#expenseForm')[0].reset();
                loadTable();
                toastr.success('Expense added successfully!');
                $('#document-preview').html(''); // Clear the document preview
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while adding the expense.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                toastr.error(errorMessage);
            }
        });
    }

    // Clear form data and handle submission
    $('#expenseForm').on('submit', function(e) {
        e.preventDefault();
        submitExpenseForm();
    });

    // Preview the selected document
    $('#document').on('change', function() {
        var file = this.files[0];
        var previewContainer = $('#document-preview');
        previewContainer.html(''); // Clear previous preview

        if (file) {
            var fileReader = new FileReader();

            fileReader.onload = function(e) {
                if (file.type.match('image.*')) {
                    previewContainer.html('<img src="' + e.target.result + '" alt="Document Preview" class="img-fluid" style="max-width: 100%;">');
                } else if (file.type === 'application/pdf') {
                    previewContainer.html('<iframe src="' + e.target.result + '" style="width: 100%; height: 400px;"></iframe>');
                } else {
                    previewContainer.text('Selected file: ' + file.name);
                }
            };

            fileReader.readAsDataURL(file);
        }
    });
  

</script>