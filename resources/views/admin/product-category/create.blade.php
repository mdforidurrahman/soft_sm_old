    {{--     Add Modal  --}}
    <div class="modal fade" id="storeModal" tabindex="-1" aria-labelledby="storeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="storeModalLabel">Add New Product Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="storeForm">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Product Category Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="submitForm()">Save Store</button>
                </div>
            </div>
        </div>
    </div>


    @push('script')
        <script>
            // Global function declaration
            function submitForm() {
                $.ajax({
                    url: "{{ route($role . 'product-category.store') }}",
                    method: 'POST',
                    data: $('#storeForm').serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#storeModal').modal('hide');
                        $('#storeForm')[0].reset();
                        loadTable();
                        AjaxNotifications.handle(response);
                    },
                    error: function(xhr) {
                        let response = JSON.parse(xhr.responseText);
                        if (xhr.status === 422) {
                            // Validation errors
                            let errorMessages = [];
                            for (let field in response.errors) {
                                errorMessages = errorMessages.concat(response.errors[field]);
                            }
                            AjaxNotifications.error(errorMessages.join('<br>'));
                        } else {
                            AjaxNotifications.error(response.message || 'An error occurred');
                        }
                        console.error('Error adding store:', response);
                    }
                });
            }

            // Event binding for form submission
            $('#storeForm').on('submit', function(e) {
                e.preventDefault();
                submitForm();
            });
        </script>
    @endpush
