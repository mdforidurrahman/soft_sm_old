{{--     Add Modal  --}}
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">Add New Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="userForm" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" name="name" id="name" class="form-control" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>

                            <input type="email" name="email" id="email" class="form-control" placeholder="Enter Email" required>

                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea name="address" class="form-control" id="address" cols="30" rows=""></textarea>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="phone">Phone</label>
                            <input type="text" name="phone" id="phone" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="role">Role</label>
                            <select name="roles[]" id="role" class="form-control select2" multiple>
                                <option value="" disabled>Select Role</option>
                                @foreach ($roles as $role)
                                <option value="{{ $role->name }}">
                                    {{ $role->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>

                        <div class="col-md-6 mt-3">
                            <div class="form-group">
                                <label for="photo">Image</label>
                                <input type="file" name="photo" class="form-control form-input" id="photo"> <br>
                                <img id="showImage" class="form-check-input" src="{{ url('upload/no_image.jpg') }}" alt="Admin" style="width:100px; height: 100px;">
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
    function submitForm() {
        var formData = new FormData($('#userForm')[0]);
        $.ajax({
            url: "{{ route('admin.' . 'user.store') }}",
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                $('#userModal').modal('hide');
                $('#userForm')[0].reset();
                loadTable();

                flasher.success('User added successfully');
            },
            error: function (xhr) {
                console.error('Error adding user:', xhr.responseText);

                let errorMessage = 'An error occurred while adding the user.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                flasher.error(errorMessage);
            }

        });


    }

    $('#userForm').on('submit', function (e) {
        e.preventDefault();
        submitForm();

    });
</script>
<script>
    $(document).ready(function () {
        $('#photo').change(function (e) {
            var file = e.target.files[0];
            var reader = new FileReader();
            reader.onload = function (event) {
                $('#showImage').attr('src', event.target.result);
            }
            reader.readAsDataURL(file);
        });
    });
</script>
