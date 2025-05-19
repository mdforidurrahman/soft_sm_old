@extends('layouts.admin')

@section('title', 'Dashboard')

@push('style')
    <link href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet">

    <style>
        .filter-dropdown {
            position: relative;
            display: inline-block;
        }

        .filter-menu {
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 1000;
            display: none;
            min-width: 200px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .filter-menu.show {
            display: block;
        }

        .filter-item {
            padding: 8px 15px;
            cursor: pointer;
        }

        .filter-item:hover {
            background-color: #f8f9fa;
        }

        .select2-container {
            min-width: 200px;
        }
      
      
      
      
      .custom-range-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1050;
}

.custom-range-modal .modal-content {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    width: 300px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.custom-range-modal h4 {
    margin-top: 0;
    margin-bottom: 16px;
    font-size: 18px;
}

.date-input-group {
    display: flex;
    gap: 10px;
    margin-bottom: 16px;
}

.date-input-group div {
    flex: 1;
}

.date-input-group label {
    display: block;
    margin-bottom: 4px;
    font-size: 14px;
}

.date-input-group input {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 8px;
}

.modal-actions button {
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.modal-actions .btn-cancel {
    background: #f0f0f0;
}

.modal-actions .btn-apply {
    background: #007bff;
    color: white;
}
    </style>
@endpush

@section('content')
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Dashboard Overview</h4>

        <div class="row g-5 justify-between justify-content-between justify-items-center">
            <div class="col-md-6 ml-3">
                <div class="filter-dropdown">
                    <select id="storeSelect" class="select2">
                        <option value="">All Stores</option>
                        @foreach ($stores as $store)
                            <option value="{{ $store->id }}">{{ $store->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="filter-dropdown">
                    <button class="btn btn-primary" id="filterBtn">
                        <i class="bx bx-calendar"></i>
                        Filter by date
                    </button>
                    <div class="filter-menu" id="filterMenu">
                        <div class="filter-item" data-range="today">Today</div>
                        <div class="filter-item" data-range="yesterday">Yesterday</div>
                        <div class="filter-item" data-range="last_7_days">Last 7 Days</div>
                        <div class="filter-item" data-range="last_30_days">Last 30 Days</div>
                        <div class="filter-item" data-range="this_month">This Month</div>
                        <div class="filter-item" data-range="last_month">Last Month</div>
                        <div class="filter-item" data-range="this_year">This Year</div>
                        <div class="filter-item" data-range="last_year">Last Year</div>
                        <div class="filter-item" data-range="custom">Custom Range</div>
                    </div>
                </div>
            </div>
        </div>


    </div>

    <div id="dashboardContent">
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4">
            @foreach ($data as $item)
                <div class="col">
                    <div class="card radius-10 {{ $item['color'] }}">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <h5 class="mb-0 text-white">{{ $item['count'] }}</h5>
                                <div class="ms-auto">
                                    <i class='bx {{ $item['icon'] }} fs-3 text-white'></i>
                                </div>
                            </div>
                            <div class="progress my-3 bg-light-transparent" style="height:3px;">
                                <div class="progress-bar bg-white" role="progressbar" style="width: 55%" aria-valuenow="25"
                                    aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="d-flex align-items-center text-white">
                                <p class="mb-0">{{ $item['title'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection

@push('script')
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        $(document).ready(function() {
            const filterBtn = $('#filterBtn');
            const filterMenu = $('#filterMenu');
            let dateRangePicker = null;
            let currentDateRange = 'today';

            $('#storeSelect').select2({
                theme: 'bootstrap-5',
                placeholder: 'Select a store',
                allowClear: true,
                width: '100%'
            });

            $('#storeSelect').on('change', function() {
                const storeId = $(this).val();
                updateDashboard(currentDateRange, null, null, storeId);
            });

            filterBtn.on('click', function(e) {
                e.stopPropagation();
                filterMenu.toggleClass('show');
            });

            $(document).on('click', function(e) {
                if (!filterMenu.is(e.target) && !filterBtn.is(e.target) && filterMenu.has(e.target)
                    .length === 0) {
                    filterMenu.removeClass('show');
                }
            });


          
          $('.filter-item').on('click', function() {
    const range = $(this).data('range');
    currentDateRange = range;

    // Highlight selected filter
    $('.filter-item').removeClass('active');
    $(this).addClass('active');

    if (range === 'custom') {
        // Show manual date input modal/form
        showCustomDateRangePicker();
    } else {
        const storeId = $('#storeSelect').val();
        updateDashboard(range, null, null, storeId);
        $('.date-range-display').text($(this).text().trim());
    }

    filterMenu.removeClass('show');
});

function showCustomDateRangePicker() {
    // Create a modal or inline form for manual date input
    const modalHtml = `
        <div class="custom-range-modal">
            <div class="modal-content">
                <h4>Select Custom Date Range</h4>
                <div class="date-input-group">
                    <div>
                        <label>From</label>
                        <input type="date" id="customStartDate" class="form-control" max="${moment().format('YYYY-MM-DD')}">
                    </div>
                    <div>
                        <label>To</label>
                        <input type="date" id="customEndDate" class="form-control" max="${moment().format('YYYY-MM-DD')}">
                    </div>
                </div>
                <div class="modal-actions">
                    <button class="btn-cancel">Cancel</button>
                    <button class="btn-apply">Apply</button>
                </div>
            </div>
        </div>
    `;

    // Append to body and show
    $('body').append(modalHtml);

    // Handle Apply button
    $('.btn-apply').on('click', function() {
        const startDate = $('#customStartDate').val();
        const endDate = $('#customEndDate').val();
        const storeId = $('#storeSelect').val();

        if (startDate && endDate) {
            updateDashboard('custom', startDate, endDate, storeId);
            $('.date-range-display').text(
                `${moment(startDate).format('MMM D, YYYY')} - ${moment(endDate).format('MMM D, YYYY')}`
            );
            $('.custom-range-modal').remove();
        } else {
            alert('Please select both dates!');
        }
    });

    // Handle Cancel button
    $('.btn-cancel').on('click', function() {
        $('.custom-range-modal').remove();
    });
}
          

            function updateDashboard(range, startDate = null, endDate = null, storeId = null) {
                // Show loading state
                $('#dashboardContent').addClass('opacity-50');

                $.ajax({
                    url: '{{ route($role . 'dashboard') }}',
                    method: 'GET',
                    data: {
                        date_range: range,
                        start_date: startDate,
                        end_date: endDate,
                        store_id: storeId
                    },
                    success: function(response) {
                        updateDashboardContent(response.data);
                        updateFilterButtonText(range, startDate, endDate);
                        // Remove loading state
                        $('#dashboardContent').removeClass('opacity-50');
                    },
                    error: function(xhr, status, error) {
                        console.error('Error updating dashboard:', error);
                        alert('Error updating dashboard. Please try again.');
                        // Remove loading state
                        $('#dashboardContent').removeClass('opacity-50');
                    }
                });
            }

            function updateDashboardContent(data) {
                let html = '<div class="row row-cols-1 row-cols-md-2 row-cols-xl-4">';

                data.forEach(item => {
                    html += `
                <div class="col">
                    <div class="card radius-10 ${item.color}">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <h5 class="mb-0 text-white">${item.count}</h5>
                                <div class="ms-auto">
                                    <i class='bx ${item.icon} fs-3 text-white'></i>
                                </div>
                            </div>
                            <div class="progress my-3 bg-light-transparent" style="height:3px;">
                                <div class="progress-bar bg-white" role="progressbar" style="width: 55%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="d-flex align-items-center text-white">
                                <p class="mb-0">${item.title}</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
                });

                html += '</div>';
                $('#dashboardContent').html(html);
            }

            function updateFilterButtonText(range, startDate = null, endDate = null) {
                let buttonText = 'Filter by date';

                switch (range) {
                    case 'today':
                        buttonText = 'Today';
                        break;
                    case 'yesterday':
                        buttonText = 'Yesterday';
                        break;
                    case 'last_7_days':
                        buttonText = 'Last 7 Days';
                        break;
                    case 'last_30_days':
                        buttonText = 'Last 30 Days';
                        break;
                    case 'this_month':
                        buttonText = 'This Month';
                        break;
                    case 'last_month':
                        buttonText = 'Last Month';
                        break;
                    case 'this_year':
                        buttonText = 'This Year';
                        break;
                    case 'last_year':
                        buttonText = 'Last Year';
                        break;
                    case 'custom':
                        buttonText = `${startDate} - ${endDate}`;
                        break;
                }

                filterBtn.html(`<i class="bx bx-calendar"></i> ${buttonText}`);
            }

            updateDashboard('this_year');
        });
    </script>
@endpush
