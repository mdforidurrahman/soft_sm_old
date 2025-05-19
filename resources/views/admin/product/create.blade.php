{{--     Add Modal  --}}
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalLabel">Add New Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="productForm" enctype="multipart/form-data">
                    @csrf

                    <div class="row mb-6">
                        <div class="col-md-6">
                            <label for="category_id" class="form-label">Product Category*</label>
                            <div class="input-group">
                                <select name="category_id" class="form-select" id="category_id" required>
                                    <option value="">Please Select</option>
                                    @forelse($productCategories as $key=>$data)
                                        <option value="{{ $data->id }}">{{ $data->name }}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="store_id" class="form-label">Store Name*</label>
                            <div class="input-group">
                                <select name="store_id" class="form-select" id="store_id" required>
                                    <option value="">Please Select</option>
                                    @forelse($storeName as $key=>$data)
                                        <option value="{{ $data->id }}">{{ $data->name }}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Name*</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="name"
                                required>
                        </div>
                        <div class="col-md-6">
                            <label for="price" class="form-label">Price*</label>
                            <input type="number" class="form-control" id="price" name="price" placeholder="price"
                                required>
                        </div>
                    </div>

                    <div class="col-md-12 mb-6">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="description" cols="30" rows="10"
                            placeholder="description"></textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="quantity" class="form-label">Quantity*</label>
                            <input type="number" class="form-control" id="quantity" name="quantity"
                                placeholder="quantity">
                        </div>
                        <div class="col-md-6">
                            <label for="min_stock" class="form-label">Minimum Stock*</label>
                            <input type="number" class="form-control" id="min_stock" name="min_stock"
                                placeholder="minimum stock">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="manage_stock" class="form-label">Manage Stock</label>
                            <div class="input-group">
                                <select class="form-select" id="manage_stock" name="manage_stock">
                                    <option value="">Please Select</option>
                                    <option value="1">In Stock</option>
                                    <option value="0">Stock out</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="image" class="form-label">Product Image</label>
                            <input type="file" class="form-control form-input" id="image" name="image">
                            <small class="form-text text-muted">Max File size: 5MB<br>Allowed File: .jpeg, .jpg,
                                .png</small>
                            <img id="showImage" class="form-check-input" src="{{ url('upload/no_image.jpg') }}"
                                alt="Admin" style="width:100px; height: 100px;">
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
        var formData = new FormData($('#productForm')[0]);

        $.ajax({
            url: "{{ route($role . 'product.store') }}",
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#productModal').modal('hide');
                $('#productForm')[0].reset();
                loadTable();

                AjaxNotifications.success('sell created successfully');
            },
            error: function(xhr) {
                let response = xhr.responseText;
                try {
                    response = JSON.parse(response);
                } catch (e) {
                    response = {
                        message: 'An error occurred'
                    };
                }

                if (xhr.status === 422) {
                    let errorMessages = [];
                    for (let field in response.errors) {
                        errorMessages = errorMessages.concat(response.errors[field]);
                    }
                    AjaxNotifications.error(errorMessages.join('<br>'));
                } else {
                    AjaxNotifications.error(response.message || 'An error occurred');
                }
                console.error('Error creating sell:', response);
            }
        });
    }

    $('#productForm').on('submit', function(e) {
        e.preventDefault();
        submitForm();
    });
</script>
<script>
    $(document).ready(function() {
        $('#photo').change(function(e) {
            var file = e.target.files[0];
            var reader = new FileReader();
            reader.onload = function(event) {
                $('#showImage').attr('src', event.target.result);
            }
            reader.readAsDataURL(file);
        });
    });
</script>
