@extends('layouts.admin')
@section('title', 'Projects List')

@push('style')
    <link href="{{ asset('backend/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet"/>
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet"/>
    <style>
        /* Modern Dashboard Card */
        .dashboard-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, .05);
            padding: 25px;
            margin: 20px 0;
        }

        .card-header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid #f5f5f5;
        }

        .table-title-section {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .table-icon {
            width: 40px;
            height: 40px;
            background: #f0f5ff;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #435ebe;
        }

        .table-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
        }

        /* Custom DataTable Styling */
        #example2 {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            margin: 15px 0;
        }

        #example2 thead th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 600;
            padding: 15px;
            border: none;
            border-bottom: 2px solid #e9ecef;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        #example2 tbody td {
            padding: 12px 15px;
            border: none;
            border-bottom: 1px solid #f1f5f9;
            color: #475569;
            font-size: 0.95rem;
            vertical-align: middle;
        }

        #example2 tbody tr:hover {
            background-color: #fafbff;
        }

        /* Export Buttons Styling */
        .dt-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .dt-button {
            padding: 8px 15px;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
            background: #f8fafc;
            color: #475569;
        }

        .dt-button:hover {
            background: #f1f5f9;
            color: #1e293b;
        }

        .dt-button i {
            font-size: 1rem;
        }

        /* Status Badge Enhanced */
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .status-active {
            background-color: #dcfce7;
            color: #166534;
        }

        .status-inactive {
            background-color: #fee2e2;
            color: #991b1b;
        }

        /* Search and Length Control Styling */
        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 6px 30px 6px 12px;
            background-color: #f8fafc;
            font-size: 0.95rem;
        }

        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 8px 15px;
            background-color: #f8fafc;
            font-size: 0.95rem;
            width: 250px;
        }

        .dataTables_wrapper .dataTables_filter input:focus {
            outline: none;
            border-color: #435ebe;
            box-shadow: 0 0 0 3px rgba(67, 94, 190, 0.1);
        }

        /* Pagination Styling */
        .dataTables_wrapper .dataTables_paginate {
            margin-top: 20px;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 8px 14px;
            margin: 0 3px;
            border-radius: 8px;
            border: none;
            font-weight: 500;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #435ebe !important;
            color: white !important;
            border: none;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #435ebe !important;
            color: white !important;
            border: none;
        }

        /* Loading State */
        .dataTables_processing {
            background: rgba(255, 255, 255, 0.9) !important;
            border-radius: 8px;
            padding: 15px !important;
            border: 1px solid #e2e8f0;
        }
    </style>
@endpush


@section('content')
    <x-breadcumb title="Projects List"/>
    <div class="table-responsive">

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-auto">
                        <button class="btn btn-warning" id="bulk-delete-btn">
                            Delete Selected Items
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-card">
            <div class="card-header-section">
                <div class="table-title-section">
                    <div class="table-icon">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <h5 class="table-title">Projects Overview</h5>
                </div>
                <div class="header-actions">
                    <button class="btn btn-danger" id="bulk-delete-btn">
                        <i class="fas fa-trash-alt me-2"></i>Bulk Delete
                    </button>
                </div>
            </div>
            <div class="table-responsive">
                <table id="example2" class="table table-hover">
                    <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="select-all" class="form-check-input">
                        </th>
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


    {{--     Project Status Modal --}}
    <div class="modal fade" id="statusChangeModal" tabindex="-1" aria-labelledby="statusChangeModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="statusChangeModalLabel">Change Project Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Change status for project: <span id="projectName"></span></p>
                    <form id="statusChangeForm">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label for="status" class="form-label">New Status</label>
                            <select class="form-select" id="status" name="status" required>
                                @foreach(App\Enums\ProjectStatus::cases() as $status)
                                    <option value="{{ $status->value }}">{{ ucfirst($status->value) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveStatusChange">Save changes</button>
                </div>
            </div>
        </div>
    </div>
@endsection

{{--@push('style')--}}
{{--    <link href="{{ asset('backend/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet"/>--}}
{{--@endpush--}}

@push('script')
    <script src="{{ asset('backend/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('backend/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('backend/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('backend/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <script>
        $(document).ready(function () {

                var table = $('#example2').DataTable({
                    lengthChange: true,
                    dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                        "<'row'<'col-sm-12'B>>" +
                        "<'row'<'col-sm-12'tr>>" +
                        "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                    buttons: [
                        {
                            extend: 'copy',
                            text: '<i class="fas fa-copy"></i> Copy',
                            className: 'btn-export'
                        },
                        {
                            extend: 'excel',
                            text: '<i class="fas fa-file-excel"></i> Excel',
                            className: 'btn-export'
                        },
                        {
                            extend: 'pdf',
                            text: '<i class="fas fa-file-pdf"></i> PDF',
                            className: 'btn-export'
                        },
                        {
                            extend: 'print',
                            text: '<i class="fas fa-print"></i> Print',
                            className: 'btn-export'
                        }
                    ],
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route($role . 'projects.index') }}',
                    columns: [
                        {
                            data: 'checkbox',
                            name: 'checkbox',
                            orderable: false,
                            searchable: false,
                            render: function (data, type, row) {
                                return `<input type="checkbox" class="form-check-input row-checkbox" value="${row.id}">`;
                            }
                        },
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
                            data: 'status',
                            name: 'status',
                            render: function (data, type, row) {
                                const icon = data === 'active' ?
                                    '<i class="fas fa-check-circle"></i>' :
                                    '<i class="fas fa-times-circle"></i>';
                                return `<span class="status-badge ${data === 'active' ? 'status-active' : 'status-inactive'}">
                                        ${icon} ${data.charAt(0).toUpperCase() + data.slice(1)}
                                   </span>`;
                            }
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    language: {
                        processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
                        emptyTable: '<div class="text-center p-4"><i class="fas fa-box-open fa-3x text-muted"></i><p class="mt-2">No data available</p></div>',
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        infoEmpty: "Showing 0 to 0 of 0 entries",
                        search: '<i class="fas fa-search"></i>',
                        searchPlaceholder: "Search projects..."
                    },
                    pageLength: 10,
                    order: [[1, 'desc']]
                });


                // Handle 'select all' checkbox
                $('#select-all').on('click', function () {
                    var rows = table.rows({
                        'search': 'applied'
                    }).nodes();
                    $('input[type="checkbox"]', rows).prop('checked', this.checked);
                });

                // Handle individual row checkbox click
                $('#userTable tbody').on('change', 'input[type="checkbox"]', function () {
                    if (!this.checked) {
                        var el = $('#select-all').get(0);
                        if (el && el.checked && ('indeterminate' in el)) {
                            el.indeterminate = true;
                        }
                    }
                });

                // Bulk delete action
                $('#bulk-delete-btn').on('click', function (e) {
                    var selectedRows = [];
                    $('input.row-checkbox:checked').each(function () {
                        selectedRows.push($(this).val());
                    });

                    if (selectedRows.length > 0) {
                        // use Swal

                        Swal.fire({
                            title: "Are you sure?",
                            text: "Once deleted, you will not be able to recover these users!",
                            icon: "warning",
                            buttons: true,
                            dangerMode: true,
                            showCancelButton: true,
                            confirmButtonColor: "#3085d6",
                            cancelButtonColor: "#d33",
                            confirmButtonText: "Yes, delete it!",
                        }).then((willDelete) => {
                            if (willDelete) {
                                $.ajax({
                                    url: '{{ route($role . 'users.bulkDelete') }}',
                                    method: 'POST',
                                    data: {
                                        ids: selectedRows,
                                        _token: '{{ csrf_token() }}'
                                    },
                                    success: function (response) {
                                        if (response.success) {
                                            alert(response.message);
                                            table.ajax
                                                .reload();
                                        } else {
                                            alert('Something went wrong!');
                                        }
                                    }
                                });
                            }
                        });


                    } else {
                        flash('error', 'Please select at least one user to delete');
                    }
                });

            });


    </script>

    <script>
        $(document).ready(function () {

            // Open modal and set project info
            $('.change-status').click(function () {
                var projectId = $(this).data('project-id');
                var projectName = $(this).closest('tr').find('td:first').text(); // Get project name from the first column
                $('#projectName').text(projectName);
                $('#statusChangeForm').attr('action', '/projects/' + projectId + '/update-status');
                $('#statusChangeModal').modal('show');
            });

            // Handle status change submission
            $('#saveStatusChange').click(function () {
                var form = $('#statusChangeForm');
                var url = form.attr('action');
                var data = form.serialize();

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: data,
                    success: function (response) {
                        $('#statusChangeModal').modal('hide');
                        // Reload the page or update the status in the table
                        location.reload();
                    },
                    error: function (xhr) {
                        alert('Error changing status. Please try again.');
                    }
                });
            });
        });
    </script>
@endpush
