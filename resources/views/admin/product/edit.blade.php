{{-- Edit Modal --}}
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
                            <label for="editcategory_id" class="form-label">Product Category*</label>
                            <div class="input-group">
                                <select name="category_id" class="form-select" id="editcategory_id" required>
                                    <option value="">Please Select</option>
                                    @forelse($productCategories as $key=>$data)
                                        <option value="{{$data->id}}">{{$data->name}}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="editstore_id" class="form-label">Store Name*</label>
                            <div class="input-group">
                                <select name="store_id" class="form-select" id="editstore_id" required>
                                    <option value="">Please Select</option>
                                    @forelse($storeName as $key=>$data)
                                        <option value="{{$data->id}}">{{$data->name}}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="editname" class="form-label">Name*</label>
                            <input type="text" class="form-control" id="editname" name="name" placeholder="name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="price" class="form-label">Price*</label>
                            <input type="number" class="form-control" id="editprice" name="price" placeholder="price" required>
                        </div>
                    </div>

                    <div class="col-md-12 mb-6">
                        <label for="editdescription" class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="editdescription" cols="30" rows="10" placeholder="description"></textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="editquantity" class="form-label">Quantity*</label>
                            <input type="number" class="form-control" id="editquantity" name="quantity" placeholder="quantity">
                        </div>
                        <div class="col-md-6">
                            <label for="editmin_stock" class="form-label">Minimum Stock*</label>
                            <input type="number" class="form-control" id="editmin_stock" name="min_stock" placeholder="minimum stock">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="editmanage_stock" class="form-label">Manage Stock</label>
                            <div class="input-group">
                                <select class="form-select" id="editmanage_stock" name="manage_stock">
                                    <option value="">Please Select</option>
                                    <option value="1">In Stock</option>
                                    <option value="0">Stock out</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="editimage" class="form-label">Product Image</label>
                            <input type="file" class="form-control form-input" id="editimage" name="image">
                            <small class="form-text text-muted">Max File size: 5MB<br>Allowed File: .jpeg, .jpg, .png</small>
                            <img id="editShowImage" class="form-check-input" src="{{ url('upload/no_image.jpg') }}" alt="Admin" style="width:100px; height: 100px;">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="updateStore()">Update Purchase</button>
            </div>
        </div>
    </div>
</div>

<script>
    function openEditModal(editUrl) {
        showLoader();
        // console.log('Opening edit modal with URL:', editUrl);

        $.ajax({
            url: editUrl,
            method: 'GET',
            success: function (response) {
                // console.log('Received response:', response);

                if (response && typeof response === 'object') {
                    $('#editId').val(response.id);
                    $('#editcategory_id').val(response.category_id);
                    $('#editname').val(response.name);
                    $('#editprice').val(response.price);
                    $('#editdescription').val(response.description);

                    // $('#editimage').val(response.image);

                    $('#editquantity').val(response.quantity);
                    $('#editstore_id').val(response.store_id );
                    $('#editmin_stock').val(response.min_stock);
                    $('#editcategory_id').val(response.category_id );
                    $('#editmanage_stock').val(response.manage_stock );

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
        const productId = $('#editId').val();
        const updateUrl = `/admin/product/${productId}`;

        // console.log('Update URL:', updateUrl);
        // console.log('Form data:', $('#editForm').serialize());
        // Create FormData object to handle file upload
        const formData = new FormData($('#editForm')[0]);

        $.ajax({
            url: updateUrl,
            method: 'POST', // Use POST, not PUT
            data: formData,
            processData: false,
            contentType: false,
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
                console.error('Error updating product:', {
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
                    alert('Error updating product. Please check the console for more information.');
                }
            }
        });
    }
</script>
<script>
    $(document).ready(function () {
        $('#editimage').change(function (e) {
            var file = e.target.files[0];
            var reader = new FileReader();
            reader.onload = function (event) {
                $('#editShowImage').attr('src', event.target.result);
            }

            reader.readAsDataURL(file);
        });
    });
</script>
