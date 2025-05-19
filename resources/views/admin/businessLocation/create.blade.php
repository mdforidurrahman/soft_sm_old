<div class="modal fade" id="businesslocationModal" tabindex="-1" aria-labelledby="businesslocationLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="businesslocationLabel">Add New Business Location</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="businessLForm" enctype="multipart/form-data">
                    @csrf

                    <div class="row mb-6">
                        <div class="col-md-6">
                            <label for="store_id" class="form-label">Store Name*</label>
                            <div class="input-group">
                                <select name="store_id" class="form-select" id="store_id" required>
                                    <option value="">Please Select</option>
                                    @forelse($storeName as $key=>$data)
                                        <option value="{{$data->id}}">{{$data->name}}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="landmark" class="form-label">Landmark*</label>
                            <input type="text" class="form-control" id="landmark" name="landmark" placeholder="landmark" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="city" class="form-label">City*</label>
                            <input type="text" class="form-control" id="city" name="city" placeholder="city" required>
                        </div>
                        <div class="col-md-6">
                            <label for="zip_code" class="form-label">Zip Code*</label>
                            <input type="text" class="form-control" id="zip_code" name="zip_code" placeholder="zip_code" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="state" class="form-label">State*</label>
                            <input type="text" class="form-control" id="state" name="state" placeholder="state">
                        </div>
                        <div class="col-md-6">
                            <label for="country" class="form-label">Country*</label>
                            <input type="text" class="form-control" id="country" name="country" placeholder="country">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status</label>
                            <div class="input-group">
                                <select class="form-select" id="status" name="status">
                                    <option value="">Please Select</option>
                                    <option value="1">Active</option>
                                    <option value="0">InActive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="submitForm()">Save Product</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Event binding for form submission
    $('#businessLForm').on('submit', function (e) {
        e.preventDefault();
        submitForm();
    });

    function submitForm() {
        $.ajax({
            url: "{{ route($role . 'business-location.store') }}",
            method: 'POST',
            data: $('#businessLForm').serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                $('#businesslocationModal').modal('hide');
                $('#businessLForm')[0].reset();
                loadTable();
                AjaxNotifications.handle(response);
            },
            error: function (xhr) {
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
</script>
