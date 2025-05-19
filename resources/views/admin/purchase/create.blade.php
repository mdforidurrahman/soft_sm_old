{{--     Add Modal  --}}

@push('style')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <style>
        .container-fluid {
            background: #fff;
            padding: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        /* Select2 Search Container Styling */
        .product-search-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 0;
            margin-bottom: 20px;
            border-bottom: 2px solid #e9ecef;
        }

        .select2-container {
            width: 100% !important;
        }

        .select2-selection {
            min-height: 45px !important;
            padding: 10px !important;
            border-radius: 4px !important;
            border: 1px solid #ced4da !important;
            font-size: 1rem;
        }

        .select2-selection__arrow {
            height: 45px !important;
        }

        /* Table Styling */
        .table {
            border: none;
            background-color: #f9fafc;
        }

        .table-header {
            background-color: #4CAF50;
            color: white;
            font-size: 1rem;
            text-align: center;
        }

        .table th,
        .table td {
            text-align: center;
            vertical-align: middle;
            padding: 12px;
        }

        .table tbody tr:hover {
            background-color: #f1f3f5;
        }

        .form-control,
        .form-select {
            border-radius: 4px !important;
            height: 38px;
        }

        /* .delete-btn {
                    color: red;
                    cursor: pointer;
                    font-size: 24px;
                    transition: all 0.2s ease;
                }

                .delete-btn:hover {
                    color: #e74c3c;
                    transform: scale(1.1);
                } */

        /* Improved product table visibility */
        #productTable {
            margin-top: 20px;
        }

        /* Add a subtle box-shadow effect */
        .table-responsive {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        /* Style the buttons and inputs */
        input[type="number"],
        select {
            height: 40px;
            font-size: 1rem;
        }

        select.form-select {
            padding: 0 12px;
        }

        /* Button Styling */


        .form-select {
            font-size: 0.9rem;
            padding: 0.375rem 1rem;
        }
    </style>
@endpush

<div class="modal fade" id="purchaseModal" tabindex="-1" aria-labelledby="purchaseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="purchaseModalLabel">Add New Purchase</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="purchaseForm">
                    @csrf
                    <!-- Basic Purchase Information -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">Purchase Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-7 me-2">
                                    <label for="supplier_id" class="form-label">Supplier*</label>
                                    {{-- <div class="input-group"> --}}

                                    <div class="row">
                                        <div class="col-md-11">
                                            <select name="supplier_id" class="form-select select2" id="supplier_id"
                                                    required>
                                                <option value="">Please Select</option>
                                                @forelse($supplier as $key=>$data)
                                                    <option value="{{ $data->id }}">{{ $data->name }}</option>
                                                @empty
                                                @endforelse
                                            </select>
                                        </div>
                                        <div class="col-md-1">
                                            <button class="btn btn-outline-secondary" type="button"
                                                    data-bs-toggle="modal" data-bs-target="#contactModal">
                                                +
                                            </button>
                                        </div>
                                    </div>

                                    {{-- </div> --}}
                                </div>
                              <!--  <div class="col-md-4 mb-2">
                                    <label for="reference_no" class="form-label">Reference No:</label>
                                    <input type="text" class="form-control" id="reference_no" name="reference_no">
                                </div> -->
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4 mb-2">
                                    <label for="purchase_date" class="form-label">Purchase Date*</label>
                                    <input type="datetime-local" class="form-control" id="purchase_date"
                                           name="purchase_date" required>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label for="purchase_status" class="form-label">Purchase Status*</label>
                                    <select class="form-select" id="purchase_status" name="purchase_status" required>
                                        <option value="">Please Select</option>
                                        <option value="pending">Pending</option>
                                        <option value="completed">Completed</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label for="business_location" class="form-label">Store Name*</label>
                                    <select class="form-select" id="business_location" name="business_store_id"
                                            required>

                                        <option value="">Please Select Store</option>

                                        @foreach ($storeName as $item)
                                            <option value="{{ $item->id }}">
                                                {{ $item->name }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>

                                <!-- <div class="col-md-4 mb-2">
                                    <label for="payment_term" class="form-label">Pay term:</label>
                                    <input type="number" class="form-control" id="payment_term" name="payment_term">

                                </div> -->
                                <!-- <div class="col-md-4 mb-2">

                                    <label for="pay_term_type" class="form-label">Pay Term Type:</label>
                                    <select class="form-" id="pay_term_type" name="payment_term_type">
                                        <option value="">Please Select</option>
                                        <option value="days">Days</option>
                                        <option value="months">Months</option>
                                    </select>
                                </div> -->
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Information -->
                 <!--   <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">Shipping Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="shipping_address" class="form-label">Shipping Address:</label>
                                    <input type="text" class="form-control" id="shipping_address"
                                           name="shipping_address">
                                </div>
                                <div class="col-md-6">
                                    <label for="shipping_method" class="form-label">Shipping Method:</label>
                                    <input type="text" class="form-control" id="shipping_method"
                                           name="shipping_method">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="shipping_cost" class="form-label">Shipping Cost:</label>
                                    <input type="number" class="form-control" id="shipping_cost"
                                           name="shipping_cost" value="0" min="0" step="0.01">
                                </div>
                                <div class="col-md-6">
                                    <label for="expected_delivery_date" class="form-label">Expected Delivery
                                        Date:</label>
                                    <input type="date" class="form-control" id="expected_delivery_date"
                                           name="expected_delivery_date">
                                </div>
                            </div>
                        </div>
                    </div>
-->
                    <!-- Products Section -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">Products</h6>
                        </div>
                        <div class="card-body">
                            <div class="product-search-container mb-3">
                                <label for="searchProduct" class="form-label">Search Products</label>
                                <select class="product-search w-100" id="searchProduct" multiple="multiple">

                                </select>
                            </div>

                            <div id="productTable" class="table-responsive" style="display: none;">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Product Name</th>
                                        <th>Purchase Quantity</th>
                                        <th>Unit Cost (Before Discount)</th>
                                        <th>Discount Percent</th>
                                        <th>Unit Cost (Before Tax)</th>
                                        <th>Subtotal (Before Tax)</th>
                                        <th>Product Tax %</th>
                                        <th>Net Cost</th>
                                        <th>Line Total</th>
                                        <th>Profit Margin %</th>
                                        <th>Unit Selling Price (Inc. tax)</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody id="productTableBody"></tbody>
                                    <tfoot>
                                    <tr>
                                        <td colspan="6" class="text-end">Total Before Tax:</td>
                                        <td colspan="2">
                                            <input type="number" class="form-control" id="total_before_tax"
                                                   name="total_before_tax" readonly>
                                        </td>
                                        <td colspan="2">Total Tax:</td>
                                        <td colspan="3">
                                            <input type="number" class="form-control" id="tax_amount"
                                                   name="tax_amount" readonly>
                                        </td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Information -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">Payment Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4 mb-2">
                                    <label for="discount_type" class="form-label">Discount Type:</label>
                                    <select class="form-select" id="discount_type" name="discount_type">
                                        <option value="">None</option>
                                        <option value="fixed">Fixed Amount</option>
                                        <option value="percentage">Percentage</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label for="discount_amount" class="form-label">Discount Amount:</label>
                                    <input type="number" class="form-control" id="discount_amount"
                                           name="discount_amount" value="0" min="0" step="0.01">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label for="net_total" class="form-label">Net Total:</label>
                                    <input type="number" class="form-control" id="net_total" name="net_total"
                                           readonly>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4 mb-2">
                                    <label for="payment_method" class="form-label">Payment Method:</label>
                                    <select class="form-select" id="payment_method" name="payment_method">
                                        <option value="cash">Cash</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                        <option value="cheque">Cheque</option>
                                        <option value="credit_card">Credit Card</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label for="advance_balance" class="form-label">Advance Balance:</label>
                                    <input type="number" class="form-control" id="advance_balance"
                                           name="advance_balance" value="0" min="0" step="0.01">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label for="payment_due" class="form-label">Payment Due:</label>
                                    <input type="number" class="form-control" id="payment_due" name="payment_due_date"
                                           readonly>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="payment_note" class="form-label">Payment Note:</label>
                                    <textarea class="form-control" id="payment_note" name="payment_note"
                                              rows="3"></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label for="additional_notes" class="form-label">Additional Notes:</label>
                                    <textarea class="form-control" id="additional_notes" name="additional_notes"
                                              rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="submitForm()">Save Purchase</button>
            </div>
        </div>
    </div>
</div>

@include('admin.contacts.create')

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        function submitForm() {
            const formData = new FormData($('#purchaseForm')[0]);

            const items = [];
            $('#productTableBody tr').each(function () {
                const row = $(this);
                const sku = row.data('sku');
                items.push({
                    product_id: sku,
                    quantity: parseFloat(row.find('.quantity-input').val()) || 0,
                    unit_cost: parseFloat(row.find('.unit-cost').val()) || 0,
                    discount_percent: parseFloat(row.find('.discount').val()) || 0,
                    profit_margin: parseFloat(row.find('.profit-margin').val()) || 0,
                    unit_cost_before_tax: parseFloat(row.find('.unit-cost-after-discount').val()) || 0,
                    tax_amount: parseFloat(row.find('.product-tax').val()) || 0,
                    net_cost: parseFloat(row.find('.net-cost').val()) || 0,
                    unit_selling_price: parseFloat(row.find('.selling-price').val()) || 0
                });
            });


            const purchaseData = {
                ...Object.fromEntries(formData),
                items: items // Pass items as an array, not a string
            };

            // Add shipping details
            if ($('#shipping_address').val()) {
                purchaseData.shipping_address = $('#shipping_address').val();
                purchaseData.shipping_method = $('#shipping_method').val();
                purchaseData.shipping_cost = parseFloat($('#shipping_cost').val()) || 0;
                purchaseData.expected_delivery_date = $('#expected_delivery_date').val();
            }

            // Add payment details if advance balance exists
            const advanceBalance = parseFloat($('#advance_balance').val() || 0);
            if (advanceBalance > 0) {
                purchaseData.advance_balance = advanceBalance;
                purchaseData.payment_method = $('#payment_method').val();
                purchaseData.payment_note = $('#payment_note').val();
            }

            $.ajax({
                url: "{{ route($role . 'purchase.store') }}",
                method: 'POST',
                data: JSON.stringify(purchaseData),
                processData: false,
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    $('#purchaseModal').modal('hide');
                    $('#purchaseForm')[0].reset();
                    loadTable();
                    AjaxNotifications.success('Purchase created successfully');
                },
                error: function (xhr) {
                    let response = xhr.responseText;
                    try {
                        response = JSON.parse(response);
                    } catch (e) {
                        response = {
                            message: 'An error occurred'
                        };
                    }

                    if (xhr.status === 422) {
                        let errorMessages = [];
                        for (let field in response.errors) {
                            errorMessages = errorMessages.concat(response.errors[field]);
                        }
                        AjaxNotifications.error(errorMessages.join('<br>'));
                    } else {
                        AjaxNotifications.error(response.message || 'An error occurred');
                    }
                    console.error('Error creating purchase:', response);
                }
            });
        }

        let productCounter = 0;
        const productData = new Map();


        function formatProduct(product) {
            if (!product.id) return product.text;

            return $(`
        <div class="d-flex justify-content-between">
            <span>${product.text}</span>
        </div>
    `);
        }

        function formatProductSelection(product) {
            return product.text;
        }

        $(document).ready(function () {
            // Initially disable product search
            $('#searchProduct').prop('disabled', true);

            // Store selection triggers product search initialization

            $('#business_location').on('change', function () {
                const storeId = $(this).val();

                if (storeId) {
                    // Clear existing products from the table
                    $('#productTableBody').empty();
                    productData.clear(); // Clear the product data map
                    $('#productTable').hide(); // Hide the product table

                    // Unselect all previously selected products
                    $('#searchProduct').val(null).trigger('change');

                    // Enable product search and reset
                    $('#searchProduct')
                        .prop('disabled', false)
                        .val(null)
                        .trigger('change');

                    // Reinitialize product search with current store context
                    initializeProductSearch();
                } else {
                    // Disable product search if no store selected
                    $('#productTableBody').empty();
                    productData.clear();
                    $('#productTable').hide();

                    $('#searchProduct')
                        .prop('disabled', true)
                        .val(null)
                        .trigger('change');
                }

                // Update totals to reset calculation
                updateTotals();
            });

            // Product selection event
            $('#searchProduct').on('select2:select', function (e) {
                addProductToTable(e.params.data);
            }).on('select2:unselect', function (e) {
                removeProductFromTable(e.params.data.id);
            });

            // Initial setup
            initializeSelects();
            initializeEventListeners();

            function initializeProductSearch() {
                $('#searchProduct').select2({
                    placeholder: 'Search Products by Name, SKU',
                    allowClear: true,
                    width: 'resolve',
                    multiple: true,
                    ajax: {
                        url: function () {
                            const url = "{{ route($role . 'products.by-store') }}";
                            console.log("Full API URL:", url);
                            return url;
                        },
                        type: 'GET',
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            const storeId = $('#business_location').val();
                            console.log("Sending request with params:", {
                                store_id: storeId,
                                search: params.term
                            });
                            return {
                                store_id: storeId,
                                search: params.term
                            };
                        },
                        processResults: function (data) {
                            console.log("API Response:", data);
                            return {
                                results: data.products.map(function (product) {
                                    return {
                                        id: product.id,
                                        text: `${product.name} (${product.sku}) - $${product.price}`,
                                        price: product.price
                                    };
                                })
                            };
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            console.error("AJAX Error:", {
                                status: jqXHR.status,
                                statusText: jqXHR.statusText,
                                responseText: jqXHR.responseText,
                                textStatus: textStatus,
                                errorThrown: errorThrown
                            });
                        }
                    },
                    minimumInputLength: 0,
                    templateResult: formatProduct,
                    templateSelection: formatProductSelection
                });
            }
        });


        function initializeSelects() {
            $('.product-search').select2({
                placeholder: 'Enter Product name / SKU / Scan bar code',
                allowClear: true,
                width: 'resolve',
                multiple: true
            });

            $('#supplier_id, #purchase_status, #business_location, #payment_term, #pay_term_type').select2({
                width: 'resolve'
            });
        }

        function initializeEventListeners() {
            // Product selection
            $('#searchProduct').on('select2:select', function (e) {
                addProductToTable(e.params.data);
            }).on('select2:unselect', function (e) {
                removeProductFromTable(e.params.data.id);
            });

            // Form submission
            $('#purchaseForm').on('submit', function (e) {
                e.preventDefault();
                submitForm();
            });

            // Initialize datepicker for purchase date
            $('#purchase_date').flatpickr({
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                defaultDate: "today"
            });

            // Initialize datepicker for expected delivery date
            $('#expected_delivery_date').flatpickr({
                dateFormat: "Y-m-d",
                minDate: "today"
            });
        }

        function addProductToTable(product) {

            $('#productTable').show();
            if (!productData.has(product.id)) {
                productCounter++;
                const price = $(product.element).data('price') || 0;

                const row = createProductRow({
                    id: product.id,
                    name: product.text,
                    price: product.price,
                    counter: productCounter
                });

                productData.set(product.id, {
                    element: row,
                    counter: productCounter
                });

                $('#productTableBody').append(row);
                updateCalculations(row);
            }
        }

        function createProductRow(product) {
            const row = $('<tr>');
            row.attr('data-sku', product.id);

            row.html(`
                <td>${product.counter}</td>
                <td>${product.name}</td>
                <td>
                    <input type="number" class="form-control quantity-input" value="1" min="1" step="1" required>
                    <select class="form-select mt-1">
                        <option selected>Pieces</option>
                        <option>Dozen</option>
                        <option>Box</option>
                    </select>
                </td>
                <td><input type="number" class="form-control unit-cost" value="${product.price}" min="0" required></td>
                <td><input type="number" class="form-control discount" value="0.00" min="0" max="100"></td>
                <td><input type="number" class="form-control unit-cost-after-discount" value="${product.price}" readonly></td>
                <td class="subtotal">${product.price}</td>
                <td><input type="number" class="form-control product-tax" value="0.00" min="0"></td>
                <td><input type="number" class="form-control net-cost" value="${product.price}" readonly></td>
                <td><input type="number" class="form-control line-total" value="${product.price}" readonly></td>
                <td><input type="number" class="form-control profit-margin" value="0.00" min="0" max="100"></td>
                <td><input type="number" class="form-control selling-price" value="${product.price}" min="0" required></td>
                <td><button type="button" class="btn btn-danger btn-sm delete-btn">×</button></td>
            `);

            // Bind input events
            row.find('input').on('input', function () {
                updateCalculations(row);
            });

            // Bind delete button
            row.find('.delete-btn').on('click', function () {
                removeProductFromTable(product.id);
            });

            return row;
        }

        function updateCalculations(row) {
            const quantity = parseFloat(row.find('.quantity-input').val()) || 0;
            const unitCost = parseFloat(row.find('.unit-cost').val()) || 0;
            const discount = parseFloat(row.find('.discount').val()) || 0;
            const tax = parseFloat(row.find('.product-tax').val()) || 0;
            const profitMargin = parseFloat(row.find('.profit-margin').val()) || 0;

            // Calculate unit cost after discount
            const unitCostAfterDiscount = unitCost * (1 - discount / 100);
            row.find('.unit-cost-after-discount').val(unitCostAfterDiscount.toFixed(2));

            // Calculate subtotal
            const subtotal = quantity * unitCostAfterDiscount;
            row.find('.subtotal').text(subtotal.toFixed(2));

            // Calculate net cost (including tax)
            const taxAmount = subtotal * (tax / 100);
            const netCost = subtotal + taxAmount;
            row.find('.net-cost').val(netCost.toFixed(2));

            // Calculate line total
            row.find('.line-total').val(netCost.toFixed(2));

            // Calculate selling price
            const sellingPrice = netCost * (1 + profitMargin / 100) / quantity;
            row.find('.selling-price').val(sellingPrice.toFixed(2));

            updateTotals();
        }

        function updateTotals() {
            let totalBeforeTax = 0;
            let totalTax = 0;
            let netTotal = 0;

            $('#productTableBody tr').each(function () {
                const row = $(this);
                totalBeforeTax += parseFloat(row.find('.subtotal').text()) || 0;
                totalTax += parseFloat(row.find('.product-tax').val() || 0) * parseFloat(row.find('.subtotal')
                    .text() || 0) / 100;
            });

            netTotal = totalBeforeTax + totalTax;

            // Update total fields
            $('#total_before_tax').val(totalBeforeTax.toFixed(2));
            $('#tax_amount').val(totalTax.toFixed(2));
            $('#net_total').val(netTotal.toFixed(2));

            // Update payment due
            const advanceBalance = parseFloat($('#advance_balance').val()) || 0;
            $('#payment_due').val((netTotal - advanceBalance).toFixed(2));
        }

        function removeProductFromTable(sku) {
            if (productData.has(sku)) {
                const data = productData.get(sku);
                data.element.remove();
                productData.delete(sku);
                updateRowNumbers();
                updateTotals();
            }
        }

        function updateRowNumbers() {
            let counter = 1;
            $('#productTableBody tr').each(function () {
                $(this).find('td:first').text(counter);
                counter++;
            });
        }
    </script>
@endpush
