{{-- View Modal --}}
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">Product Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-6">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Product Category</label>
                        <p id="viewCategory"></p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Store Name</label>
                        <p id="viewStore"></p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Name</label>
                        <p id="viewName"></p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Price</label>
                        <p id="viewPrice"></p>
                    </div>
                </div>

                <div class="col-md-12 mb-6">
                    <label class="form-label fw-bold">Description</label>
                    <p id="viewDescription"></p>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Quantity</label>
                        <p id="viewQuantity"></p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Minimum Stock</label>
                        <p id="viewMinStock"></p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Stock Status</label>
                        <p id="viewManageStock"></p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Product Image</label>
                        <div>
                            <img id="viewImage" src="{{ url('upload/no_image.jpg') }}" alt="Product Image" style="width:100px; height: 100px;">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function openViewModal(showUrl) {
    $.ajax({
        url: showUrl,
        method: 'GET',
        success: function(response) {
            // Populate the modal with data
            $('#viewCategory').text(response.category.name);
            $('#viewStore').text(response.store.name);
            $('#viewName').text(response.name);
            $('#viewPrice').text(response.price);
            $('#viewDescription').text(response.description);
            $('#viewQuantity').text(response.quantity);
            $('#viewMinStock').text(response.min_stock);
            $('#viewManageStock').text(response.manage_stock == 1 ? 'In Stock' : 'Stock out');

            // Handle image
            if (response.image) {
                $('#viewImage').attr('src', `${response.image}`);
            }

            // Show the modal
            $('#viewModal').modal('show');
        },
        error: function(xhr) {
            console.error('Error fetching product details:', xhr);
            alert('Error fetching product details');
        }
    });
}
</script>
