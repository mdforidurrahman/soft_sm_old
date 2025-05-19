{{--     Add Modal  --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Add New Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <input type="hidden" id="editId" name="id">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="editname" class="form-label">Name</label>
                                <input type="text" name="name" id="editname" class="form-control" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="editemail" class="form-label">Email</label>

                            <input type="email" name="email" id="editemail" class="form-control"
                                   placeholder="Enter Email" required>

                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="editaddress" class="form-label">Address</label>
                            <textarea name="address" class="form-control" id="editaddress" cols="30" rows=""></textarea>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="editphone">Phone</label>
                            <input type="text" name="phone" id="editphone" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editrole">Role</label>
                            <select name="roles[]" id="editrole" class="form-control select2" multiple>
                                <option value="" disabled>Select Role</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}">
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>


                        <div class="col-md-6 mt-3">
                            <div class="form-group">
                                <label for="editphoto">Image</label>
                                <input type="file" name="photo" class="form-control form-input" id="editphoto"> <br>
                                <img id="editshowImage" class="form-check-input" src="{{ url('upload/no_image.jpg') }}"
                                     alt="Admin" style="width:100px; height: 100px;">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="updateStore()">Save Product</button>
            </div>
        </div>
    </div>
</div>

<script>
    function openEditModal(editUrl) {
        showLoader();
        //console.log('Opening edit modal with URL:', editUrl);

        $.ajax({
            url: editUrl,
            method: 'GET',
            success: function (response) {
                //console.log('Received response:', response);

                if (response && typeof response === 'object') {
                    $('#editId').val(response.id);
                    $('#editname').val(response.name);
                    $('#editemail').val(response.email);
                    $('#editaddress').val(response.address);

                    $('#editphone').val(response.phone);
                    $('#editrole').val(response.role);
                    // $('#editrole').val(response.roles).trigger('change');

                    if (response.photo && response.photo !== null) {
                        $('#editshowImage').attr('src', window.location.origin + "/" + response.photo);
                    } else {
                        $('#editshowImage').attr('src', window.location.origin + "/upload/no_image.jpg");
                    }
                    // Note: We don't set the document input, as it's a file input

                    $('#editModal').modal('show');
                } else {
                    console.error('Invalid response format:', response);
                    alert('Received invalid data from the server.');
                }

                hideLoader();
            },
            error: function (xhr, status, error) {
                console.error('Error fetching purchase data:', {
                    status: status,
                    error: error,
                    responseText: xhr.responseText
                });
                hideLoader();
                alert('Error fetching purchase data. Please check the console for more information.');
            }
        });
    }

    // Update Function
    function updateStore() {
        showLoader();
        const userId = $('#editId').val();
        const updateUrl = `/admin/user/${userId}`;

        let formData = new FormData($('#editForm')[0]); // Create a FormData object

        // console.log('Update URL:', updateUrl);

        $.ajax({
            url: updateUrl,
            method: 'POST',
            data: formData,
            contentType: false, // Prevent jQuery from setting content type
            processData: false, // Prevent jQuery from processing the data
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                // console.log('Update successful:', response);
                $('#editModal').modal('hide');
                hideLoader();
                loadTable();
            },
            error: function (xhr, status, error) {
                console.error('Error updating contact:', {
                    status: status,
                    error: error,
                    responseText: xhr.responseText,
                    xhr: xhr
                });
                hideLoader();
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    let errorMessage = 'Validation errors:\n';
                    for (let field in xhr.responseJSON.errors) {
                        errorMessage += `${field}: ${xhr.responseJSON.errors[field].join(', ')}\n`;
                    }
                    alert(errorMessage);
                } else {
                    alert('Error updating contact. Please check the console for more information.');
                }
            }
        });
    }
</script>
<script>
    $(document).ready(function () {
        $('#editphoto').change(function (e) {
            var file = e.target.files[0];
            var reader = new FileReader();
            reader.onload = function (event) {
                $('#editshowImage').attr('src', event.target.result);
            }

            reader.readAsDataURL(file);
        });
    });
</script>
