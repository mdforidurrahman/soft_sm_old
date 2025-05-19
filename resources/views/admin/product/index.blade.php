@extends('layouts.admin')
@section('title', 'Product List')

@push('style')
    @include('import.css.datatable')
@endpush

@section('content')
    <x-breadcumb title="Product List" />
    <div class="table-responsive">


        <div class="dashboard-card">
            <div class="card-header-section">
                <div class="table-title-section">
                    <div class="table-icon">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <h5 class="table-title">Product Overview</h5>
                </div>
                <div class="header-actions">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal">
                        Add New Product
                    </button>

                </div>
            </div>
            <div class="table-responsive">
                <table id="example2" class="table table-hover">
                    <thead>
                        <tr class="text-capitalize">
                            <th>SL</th>
                            <th>Action</th>
                            <th>Status</th>
                            <th>Image</th>
                            <th>name</th>
                            <th>price</th>
                            <th>quantity</th>
                            <th>Store Name</th>
                            <th>min stock</th>
                            <th>Category Name</th>
                            <th>sku</th>
                            <th>manage stock</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('script')
    @include('import.js.datatable')

    @include('admin.product.create', $productCategories)

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
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'image',
                        name: 'image'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },

                    {
                        data: 'price',
                        name: 'price'
                    },
                    {
                        data: 'quantity',
                        name: 'quantity'
                    },
                    {
                        data: 'store_id',
                        name: 'store_id'
                    }, {
                        data: 'min_stock',
                        name: 'min_stock'
                    }, {
                        data: 'category_id',
                        name: 'category_id'
                    },
                    {
                        data: 'sku',
                        name: 'sku'
                    }, {
                        data: 'manage_stock',
                        name: 'manage_stock'
                    }

                ];
            initDataTable
            (
                '#example2',
                '{{ route($role . 'product.index') }}',
                columns
            )
        }
    </script>

    @include('admin.product.edit')
    @include('admin.product.show')
@endpush
