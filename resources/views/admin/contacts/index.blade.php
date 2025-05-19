@extends('layouts.admin')
@section('title', 'Contacts List')

@push('style')
	@include('import.css.datatable')

@endpush

@section('content')
	<x-breadcumb title="Projects List"/>
	<div class="table-responsive">
		<div class="dashboard-card">
			<div class="card-header-section">
				<div class="table-title-section">
					<div class="table-icon">
						<i class="fas fa-project-diagram"></i>
					</div>
					<h5 class="table-title">Projects Overview</h5>
				</div>
				<div class="header-actions">
					<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#contactModal">
						Add New Contacts
					</button>
				</div>
			</div>
			<div class="table-responsive">
				<table id="example2" class="table table-hover">
					<thead>
					<tr>

						<th>SL</th>
						<th>Action</th>
						<th>Status</th>
						<th>Contact Id</th>
						<th>Name</th>
						<th>Role</th>
						<th>Phone</th>
						<th>NID</th>
						<th>Address</th>

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

	@include('admin.contacts.create')
	<script>
        loadTable();

        function loadTable() {

            const columns = [


                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                }, {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }, {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'contact_id',
                    name: 'contact_id'
                },
                {
                    data: 'name',
                    name: 'name'
                }, {
                    data: 'role',
                    name: 'role'
                },
                {
                    data: 'phone',
                    name: 'phone'
                }, {
                    data: 'nid',
                    name: 'nid'
                },
                {
                    data: 'address',
                    name: 'address'
                }
            ];
            initDataTable(
                '#example2',
                '{{ route($role . 'contacts.index') }}',
                columns
            );
        }
	</script>

	@include('admin.contacts.edit')
@endpush