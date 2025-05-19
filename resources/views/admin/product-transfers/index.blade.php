@extends('layouts.admin')
@section('title', 'Product Transfer List')

@push('style')
@include('import.css.datatable')
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Initiate Transfer</div>
                <div class="card-body">
                    <form id="transferForm">
                        @csrf
                        <div class="form-group">
                            <label>From Store</label>
                            <select name="from_store_id" id="fromStoreSelect" class="form-control" required>
                                <option value="">Select From Store</option>
                                @foreach ($stores as $store)
                                <option value="{{ $store->id }}">{{ $store->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>To Store</label>
                            <select name="to_store_id" id="toStoreSelect" class="form-control" required>
                                <option value="">Select To Store</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Product</label>
                            <select name="store_product_id" id="productSelect" class="form-control" required disabled>
                                <option value="">Select Product</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Quantity</label>
                            <input type="number" name="quantity" id="quantityInput" class="form-control" required
                                min="1" disabled>
                        </div>
                        <button type="submit" class="btn btn-primary form-control my-3 " id="submitTransfer"
                            disabled>Initiate
                            Transfer
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Product Transfers</div>
                <div class="card-body">
                    <table class="table table-bordered table-striped" id="example2">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>From Store</th>
                                <th>To Store</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
@include('import.js.datatable')

<script>


    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('#example2').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route($role . 'product-transfers.index') }}',
            columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
            },
            {
                data: 'from_store',
                name: 'fromStore.name'
            },
            {
                data: 'to_store',
                name: 'toStore.name'
            },
            {
                data: 'product',
                name: 'storeProduct.name'
            },
            {
                data: 'quantity',
                name: 'quantity'
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
        ]
            });

    $('#transferForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: '{{ route($role . 'product-transfers.initiate') }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function (response) {
                if (response.success) {
                    $('#example2').DataTable().ajax.reload();
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr) {
                toastr.error('An error occurred');
            }
                });
            });
    $(document).on('click', '.transfer-accept, .transfer-reject', function () {
        const transferId = $(this).data('id');
        const action = $(this).hasClass('transfer-accept') ? 'accept' : 'reject';
        const url = action === 'accept' ?
            '{{ route($role . 'product-transfers.accept') }}' :
        '{{ route($role . 'product-transfers.reject') }}';

        $.ajax({
            url: url,
            method: 'PATCH',
            data: {
                id: transferId
            },
            success: function (response) {
                if (response.success) {
                    $('#transfersTable').DataTable().ajax.reload();
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr) {
                toastr.error('An error occurred');
            }
        });
    });
        });

    $(function () {
        // Store all products and stores
        const allStores = @json($stores);
        const allProducts = @json($storeProducts);

        // From Store selection
        $('#fromStoreSelect').on('change', function () {
            const fromStoreId = $(this).val();

            // Reset and disable downstream fields
            $('#toStoreSelect').html('<option value="">Select To Store</option>');
            $('#productSelect').html('<option value="">Select Product</option>').prop('disabled', true);
            $('#quantityInput').val('').prop('disabled', true);
            $('#submitTransfer').prop('disabled', true);

            // Populate To Store options (excluding selected From Store)
            allStores.forEach(store => {
                if (store.id != fromStoreId) {
                    $('#toStoreSelect').append(`<option value="${store.id}">${store.name}</option>`);
                }
            });
        });

        // To Store selection
        $('#toStoreSelect').on('change', function () {
            const fromStoreId = $('#fromStoreSelect').val();
            const toStoreId = $(this).val();

            // Reset and disable downstream fields
            $('#productSelect').html('<option value="">Select Product</option>').prop('disabled', true);
            $('#quantityInput').val('').prop('disabled', true);
            $('#submitTransfer').prop('disabled', true);

            // Filter and populate products based on From Store
            const filteredProducts = allProducts.filter(product =>
                product.store_id == fromStoreId
            );

            // Populate Product options
            filteredProducts.forEach(product => {
                $('#productSelect')
                    .append(`<option value="${product.id}">
                    ${product.name} (${product.store.name} - Qty: ${product.quantity})
                </option>`)
                    .prop('disabled', false);
            });
        });

        // Product selection
        $('#productSelect').on('change', function () {
            const selectedProduct = allProducts.find(
                product => product.id == $(this).val()
            );

            if (selectedProduct) {
                // Enable quantity input
                $('#quantityInput')
                    .prop('disabled', false)
                    .attr('max', selectedProduct.quantity)
                    .val(1);
            }
        });

        // Quantity input validation
        $('#quantityInput').on('input', function () {
            const selectedProduct = allProducts.find(
                product => product.id == $('#productSelect').val()
            );

            if (selectedProduct) {
                const maxQuantity = selectedProduct.quantity;
                const currentValue = parseInt($(this).val());

                // Ensure quantity is not negative and not exceeding available stock
                if (currentValue > maxQuantity) {
                    $(this).val(maxQuantity);
                } else if (currentValue < 1) {
                    $(this).val(1);
                }

                // Enable submit only when all fields are filled and quantity is valid
                const isFormValid =
                    $('#fromStoreSelect').val() &&
                    $('#toStoreSelect').val() &&
                    $('#productSelect').val() &&
                    currentValue > 0 &&
                    currentValue <= maxQuantity;

                $('#submitTransfer').prop('disabled', !isFormValid);
            }
        });
    });
</script>
@endpush