@extends('layouts.admin')
@section('title', 'Expense Categories')

@push('style')
    @include('import.css.datatable')
    <!-- Include any additional CSS for Toast notifications -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endpush

@section('content')
    <x-breadcumb title="Expense Categories"/>
    <div class="table-responsive">
        <div class="dashboard-card">
            <div class="card-header-section">
                <div class="table-title-section">
                    <div class="table-icon">
                        <i class="fas fa-folder"></i>
                    </div>
                    <h5 class="table-title">Expense Categories</h5>
                </div>
                <!-- Add Category Button -->
                <button class="btn btn-primary mb-3" id="openModalBtn">
                    + Add Category
                </button>
            </div>
            <div class="table-responsive">
                <table id="expenseCategoryTable" class="table table-hover">
                    <thead>
                    <tr>
                        <th>SL</th>
                        <th>Category Name</th>
                        <th>Category Code</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add/Edit Category Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoryModalLabel">Add Expense Category</h5>
                    <button type="button" class="close close-modal-btn" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="categoryForm">
                        @csrf
                        <input type="hidden" id="categoryId" name="categoryId">
                        <div class="form-group">
                            <label for="name">Category Name:</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="code">Category Code:</label>
                            <input type="text" class="form-control" id="code" name="code" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Save</button>
                        <button type="button" class="btn btn-secondary close-modal-btn" data-dismiss="modal">Close</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    @include('import.js.datatable')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script> <!-- Toast library -->
    <script>
        $(document).ready(function() {
            // Initialize DataTable with export buttons
            const table = $('#expenseCategoryTable').DataTable({
                lengthChange: true,
                processing: true,
                serverSide: true,
                ajax: '{{ route($role . "expensecategory.addcategory") }}',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'code', name: 'code' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'copy',
                        text: '<i class="fas fa-copy"></i> Copy',
                        className: 'btn btn-info'
                    },
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        className: 'btn btn-success'
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        className: 'btn btn-danger'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Print',
                        className: 'btn btn-info'
                    }
                ]
            });

            // Show modal for adding a new category
            $('#openModalBtn').on('click', function() {
                $('#categoryForm')[0].reset(); // Reset the form
                $('#categoryId').val(''); // Clear the hidden category ID
                $('#addCategoryModalLabel').text('Add Expense Category'); // Set modal title
                $('#categoryForm').attr('action', '{{ route($role . "expensecategory.storecategory") }}'); // Set form action for adding
                $('#addCategoryModal').modal('show');
            });


            // Handle Edit button click
            $(document).on('click', '.edit-btn', function() {
                const categoryId = $(this).data('id');
                $.ajax({
                    url: `{{ route($role . "expensecategory.editcategory", ":id") }}`.replace(':id', categoryId),
                    method: 'GET',
                    success: function(data) {
                        $('#name').val(data.name);
                        $('#code').val(data.code);
                        $('#categoryId').val(categoryId); // Set the hidden category ID
                        $('#addCategoryModalLabel').text('Edit Expense Category'); // Set modal title
                        $('#categoryForm').attr('action', `{{ route($role . "expensecategory.updatecategory", ":id") }}`.replace(':id', categoryId));
                        $('#addCategoryModal').modal('show');
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        toastr.error('Error loading category data.');
                    }
                });
            });

            // Handle form submission for both add and edit
            $('#categoryForm').on('submit', function(e) {
                e.preventDefault();
                const actionUrl = $(this).attr('action');
                $.ajax({
                    url: actionUrl,
                    method: 'POST',
                    data: $(this).serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#addCategoryModal').modal('hide');
                        table.ajax.reload();
                        toastr.success('Category saved successfully!');
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        toastr.error('Error saving category.');
                    }
                });
            });

            // Handle Delete button click
            $(document).on('click', '.delete-btn', function() {
                const categoryId = $(this).data('id');

                // Use SweetAlert2 for confirmation
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you really want to delete this category?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ route($role . "expensecategory.deletecategory", ":id") }}`.replace(':id', categoryId),
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                table.ajax.reload();
                                // Show success toast notification
                                toastr.success('Category deleted successfully!');
                            },
                            error: function(xhr) {
                                console.error(xhr.responseText);
                                // Show error toast notification
                                toastr.error('Error deleting category.');
                            }
                        });
                    }
                });
            });


            // Fix for modal close buttons
            $('.close-modal-btn').on('click', function() {
                $('#addCategoryModal').modal('hide');
            });
        });
    </script

@endpush
