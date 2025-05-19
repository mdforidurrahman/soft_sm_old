@extends('layouts.admin')

@section('title', 'Product Category')

@push('style')
    @include('import.css.datatable')
@endpush

@section('content')

    <x-breadcumb title="Product Category"/>

    <div class="dashboard-card">
        <div class="card-header-section">
            <div class="table-title-section">
                <div class="table-icon">
                    <i class="fas fa-project-diagram"></i>
                </div>
                <h5 class="table-title">Product Category Overview</h5>
            </div>
            <div class="header-actions">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#storeModal">
                    Add New Store
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





    @include('admin.product-category.create')
    @include('admin.product-category.edit')

@endsection



@push('script')
    @include('import.js.datatable')

    <script>
        loadTable();

        function loadTable() {

            const columns = [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'name',
                    name: 'name'
                }, {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ];
            initDataTable
            (
                '#example2',
                '{{ route($role . 'product-category.index') }}',
                columns
            )
        }
    </script>
@endpush
