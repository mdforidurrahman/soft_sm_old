@extends('layouts.admin')
@section('title', 'Business Location List')

@push('style')
    @include('import.css.datatable')
@endpush

@section('content')
    <x-breadcumb title="Business Location List"/>
    <div class="table-responsive">


        <div class="dashboard-card">
            <div class="card-header-section">
                <div class="table-title-section">
                    <div class="table-icon">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <h5 class="table-title">Business Location Overview</h5>
                </div>
                <div class="header-actions">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#businesslocationModal">
                        Add New Business Location
                    </button>

                </div>
            </div>
            <div class="table-responsive">
                <table id="example2" class="table table-hover">
                    <thead>
                    <tr>
                        <th>SL</th>
                        <th>Store Name</th>
                        <th>Landmark</th>
                        <th>City</th>
                        <th>Zip Code</th>
                        <th>State</th>
                        <th>Country</th>
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

    {{--   add new --}}
    @include('admin.businessLocation.create',$storeName)

    {{--     Edit Modal --}}
    @include('admin.businessLocation.edit',$storeName)
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
                    data: 'store_id',
                    name: 'store_id',
                    orderable: true,
                },
                {
                    data: 'landmark',
                    name: 'landmark',
                    orderable: true,
                },
                {
                    data: 'city',
                    name: 'city',
                    orderable: true,
                },
                {
                    data: 'zip_code',
                    name: 'zip_code',
                    orderable: true,
                },
                {
                    data: 'state',
                    name: 'state',
                    orderable: true,
                },{
                    data: 'country',
                    name: 'country',
                    orderable: true,
                },
                {
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
                '{{ route($role . 'business-location.index') }}',
                columns
            )
        }

    </script>
@endpush


