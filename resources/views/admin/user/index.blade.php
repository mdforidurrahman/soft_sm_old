@extends('layouts.admin')

@section('title', 'User List')
@push('style')
    @include('import.css.datatable')

    <style>
        .store-item {
            display: block;
            margin-bottom: 3px;
            line-height: 1.5;
        }
    </style>
@endpush

@section('content')

    <x-breadcumb title="User List"/>
    <div class="table-responsive">
        <div class="dashboard-card">
            <div class="card-header-section">
                <div class="table-title-section">
                    <div class="table-icon">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <h5 class="table-title">User Overview</h5>
                </div>
                <div class="header-actions">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#userModal">
                        Add New User
                    </button>

                </div>
            </div>
            <div class="table-responsive">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example2" class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Assigned Stores</th>
                                    <th>Store Assignment</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>


        <!-- Assign Stores Modal -->
        <div class="modal fade" id="assignStoresModal" tabindex="-1" aria-labelledby="assignStoresModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="assignStoresModalLabel">Assign Stores to User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="assignStoresForm">
                            <input type="hidden" id="assignStoresUserId" name="user_id">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="storeSelect">Select Stores</label>
                                        <select id="storeSelect" class="form-control select2" multiple="multiple"
                                                name="stores[]">
                                            @foreach($stores as $store)
                                                <option value="{{ $store->id }}">{{ $store->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="saveAssignedStores">Save Stores</button>
                    </div>
                </div>
            </div>
        </div>

        @endsection


        @push('script')
            @include('import.js.datatable')

            @include('admin.user.create',$roles)

            <script>
                loadTable();

                function loadTable() {

                    const columns =[
                        {
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'phone',
                            name: 'phone'
                        },
                        {
                            data: 'email',
                            name: 'email'
                        },
                        {
                            data: 'role',
                            name: 'role'
                        },
                        {
                            data: 'stores',
                            name: 'stores',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'store_assignment',
                            name: 'store_assignment',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'status',
                            name: 'status'
                        },
                        {
                            data: 'action',
                            name: 'action'
                        },
                    ];
                    initDataTable
                    (
                        '#example2',
                        '{{ route($role . 'user.index') }}',
                        columns
                    )
                }
            </script>

            <script>
                // Add this to your existing script
                $(document).ready(function () {
                    // Triggered when the assign stores button is clicked
                    $(document).on('click', '.assign-stores-btn', function () {
                        const userId = $(this).data('id');
                        $('#assignStoresUserId').val(userId);

                        // Clear previous selections
                        $('#storeSelect').val([]);

                        // Fetch current assigned stores
                        $.ajax({
                            url: `/users/${userId}/stores`,
                            method: 'GET',
                            success: function (response) {
                                // Preselect currently assigned stores
                                const assignedStoreIds = response.assigned_stores.map(store => store.id);
                                $('#storeSelect').val(assignedStoreIds);
                            }
                        });

                        // Show the modal
                        $('#assignStoresModal').modal('show');
                    });

                    // Save assigned stores
                    $('#saveAssignedStores').on('click', function () {
                        const userId = $('#assignStoresUserId').val();
                        const selectedStores = $('#storeSelect').val();

                        $.ajax({
                            url: `/users/${userId}/assign-stores`,
                            method: 'POST',
                            data: {
                                stores: selectedStores,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function (response) {
                                toastr.success(response.message);
                                $('#assignStoresModal').modal('hide');

                                // Optionally refresh the datatable
                                $('#example2').DataTable().ajax.reload();
                            },
                            error: function (xhr) {
                                toastr.error('Failed to assign stores');
                            }
                        });
                    });
                });
            </script>
    @include('admin.user.edit',$roles)
    @endpush
