{{--     Edit Modal --}}
<div class="modal fade" id="editStoreModal" tabindex="-1" aria-labelledby="editStoreModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editStoreModalLabel">Edit Product Category </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editStoreForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editStoreId" name="id">
                    <div class="mb-3">
                        <label for="editName" class="form-label">Product Category Name</label>
                        <input type="text" class="form-control" id="editName" name="name" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="updateStore()">Update Store</button>
            </div>
        </div>
    </div>
</div>



@push('script')
    <script>
        function openEditModal(editUrl) {
            showLoader();
            console.log('Opening edit modal with URL:', editUrl);

            $.ajax({
                url: editUrl,
                method: 'GET',
                success: function(response) {
                    console.log('Received response:', response);

                    if (response && typeof response === 'object') {
                        $('#editStoreId').val(response.id);
                        $('#editName').val(response.name);



                        $('#editStoreModal').modal('show');
                    } else {
                        console.error('Invalid response format:', response);
                        alert('Received invalid data from the server.');
                    }

                    hideLoader();
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching store data:', {
                        status: status,
                        error: error,
                        responseText: xhr.responseText
                    });
                    hideLoader();
                    alert('Error fetching store data. Please check the console for more information.');
                }
            });
        }

        function updateStore() {
            showLoader();
            const storeId = $('#editStoreId').val();
            const updateUrl = "{{ route($role . 'product-category.update', ':id') }}".replace(':id', storeId);

            $.ajax({
                url: updateUrl,
                method: 'POST',
                data: $('#editStoreForm').serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log('Store updated successfully:', response);
                    $('#editStoreModal').modal('hide');
                    hideLoader();
                    // location.reload();
                    loadTable();
                    AjaxNotifications.handle(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error updating store:', {
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
                        alert('Error updating store. Please try again.');
                    }
                }
            });
        }
    </script>
@endpush
