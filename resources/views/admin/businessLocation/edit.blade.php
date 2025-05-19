<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Purchase</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <input type="hidden" id="editId" name="id">

                    <div class="row mb-6">
                        <div class="col-md-6">
                            <label for="editstore_id" class="form-label">Store Name*</label>
                            <div class="input-group">
                                <select name="editstore_id" class="form-select" id="editstore_id" required>
                                    <option value="">Please Select</option>
                                    @forelse($storeName as $key=>$data)
                                        <option value="{{$data->id}}">{{$data->name}}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="editlandmark" class="form-label">Landmark*</label>
                            <input type="text" class="form-control" id="editlandmark" name="editlandmark" placeholder="landmark" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="editcity" class="form-label">City*</label>
                            <input type="text" class="form-control" id="editcity" name="editcity" placeholder="city" required>
                        </div>
                        <div class="col-md-6">
                            <label for="editzip_code" class="form-label">Zip Code*</label>
                            <input type="text" class="form-control" id="editzip_code" name="editzip_code" placeholder="zip_code" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="editstate" class="form-label">State*</label>
                            <input type="text" class="form-control" id="editstate" name="editstate" placeholder="state">
                        </div>
                        <div class="col-md-6">
                            <label for="editcountry" class="form-label">Country*</label>
                            <input type="text" class="form-control" id="editcountry" name="editcountry" placeholder="country">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="editstatus" class="form-label">Status</label>
                            <div class="input-group">
                                <select class="form-select" id="editstatus" name="editstatus">
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
                <button type="button" class="btn btn-primary" onclick="updateBusinessLocation()">Update</button>
            </div>
        </div>
    </div>
</div>

<script>
    function openEditModal(editUrl) {
        showLoader();
        console.log('Opening edit modal with URL:', editUrl);

        $.ajax({
            url: editUrl,
            method: 'GET',
            success: function (response) {
                console.log('Received response:', response);

                if (response && typeof response === 'object') {
                    $('#editId').val(response.id);
                    $('#editstore_id').val(response.store_id);
                    $('#editlandmark').val(response.landmark);
                    $('#editcity').val(response.city);
                    $('#editzip_code').val(response.zip_code);
                    $('#editstate').val(response.state);
                    $('#editcountry').val(response.country);
                    $('#editstatus').val(response.status);

                    console.log('Populated form fields:', {
                        id: $('#editId').val(),
                        store_id: $('#editstore_id').val(),
                        landmark: $('#editlandmark').val(),
                        city: $('#editcity').val(),
                        zip_code: $('#editzip_code').val(),
                        state: $('#editstate').val(),
                        country: $('#editcountry').val(),
                        status: $('#editstatus').val(),

                    });

                    $('#editModal').modal('show');
                } else {
                    console.error('Invalid response format:', response);
                    alert('Received invalid data from the server.');
                }

                hideLoader();
            },
            error: function (xhr, status, error) {
                console.error('Error fetching business-location data:', {
                    status: status,
                    error: error,
                    responseText: xhr.responseText
                });
                hideLoader();
                alert('Error fetching business-location data. Please check the console for more information.');
            }
        });
    }

    function updateBusinessLocation() {
        showLoader();
        const businesslId = $('#editId').val();
        const updateUrl = "{{ route($role . 'business-location.update', ':id') }}".replace(':id', businesslId);

        $.ajax({
            url: updateUrl,
            method: 'POST',
            data: $('#editForm').serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                console.log('Business Location updated successfully:', response);
                $('#editModal').modal('hide');
                loadTable();
                hideLoader();
                AjaxNotifications.handle(response);
                // location.reload();
            },
            error: function (xhr, status, error) {
                console.error('Error updating Business Location:', {
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
                    AjaxNotifications.handle(response);
                } else {
                    alert('Error updating Business Location. Please try again.');
                }
            }
        });
    }
</script>
