<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Sell</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editsellForm">
                    @csrf
                    <input type="hidden" name="sell_id" id="edit_sell_id">

                    <!-- Basic sell Information -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">Sell Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="edit_customer_id" class="form-label">Customer*</label>
                                    <div class="input-group">
                                        <select name="customer_id" class="form-select" id="edit_customer_id" required>
                                            <option value="">Please Select</option>
                                            @forelse($supplier as $key=>$data)
                                                <option value="{{ $data->id }}">{{ $data->name }}</option>
                                            @empty
                                            @endforelse
                                        </select>
                                        <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal"
                                                data-bs-target="#contactModal">
                                            +
                                        </button>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label for="store_id" class="form-label">Store*</label>
                                    <div class="input-group">
                                        <select name="store_id" class="form-select" id="edit_store_id" required>
                                            <option value="">Please Select any</option>
                                            @forelse($storeName as $key=>$data)
                                                <option value="{{ $data->id }}">{{ $data->name }}</option>
                                            @empty
                                            @endforelse
                                        </select>

                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="category_id" class="form-label">Category*</label>
                                    <div class="input-group">
                                        <select name="category_id" class="form-select" id="edit_category_id" required>
                                            <option value="">Please Select any</option>
                                            @forelse($categories as $key=>$data)
                                                <option value="{{ $data->id }}">{{ $data->name }}</option>
                                            @empty
                                            @endforelse
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="edit_sell_date" class="form-label">Sell Date*</label>
                                    <input type="datetime-local" class="form-control" id="edit_sell_date"
                                           name="sell_date" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_sell_status" class="form-label">Sell Status*</label>
                                    <select class="form-select" id="edit_sell_status" name="sell_status" required>
                                        <option value="">Please Select</option>
                                        <option value="pending">Pending</option>
                                        <option value="completed">Completed</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="edit_reference_no" class="form-label">Reference No:</label>
                                    <input type="text" class="form-control" id="edit_reference_no"
                                           name="reference_no">
                                </div>

                                <div class="col-md-3">
                                    <label for="edit_pay_term_type" class="form-label">Pay Term:</label>
                                    <select class="form-select" id="edit_pay_term_type" name="edit_pay_term_type">
                                        <option value="">Please Select</option>
                                        <option value="days">Days</option>
                                        <option value="months">Months</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="edit_pay_term" class="form-label">Pay Term Number:</label>
                                    <input type="number" class="form-control" id="edit_pay_term" name="edit_pay_term">
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Shipping Information -->
                    {{--                    <div class="card mb-3">--}}
                    {{--                        <div class="card-header">--}}
                    {{--                            <h6 class="mb-0">Shipping Details</h6>--}}
                    {{--                        </div>--}}
                    {{--                        <div class="card-body">--}}
                    {{--                            <div class="row mb-3">--}}
                    {{--                                <div class="col-md-6">--}}
                    {{--                                    <label for="edit_shipping_address" class="form-label">Shipping Address:</label>--}}
                    {{--                                    <input type="text" class="form-control" id="edit_shipping_address"--}}
                    {{--                                        name="shipping_address">--}}
                    {{--                                </div>--}}
                    {{--                                <div class="col-md-6">--}}
                    {{--                                    <label for="edit_shipping_method" class="form-label">Shipping Method:</label>--}}
                    {{--                                    <input type="text" class="form-control" id="edit_shipping_method"--}}
                    {{--                                        name="shipping_method">--}}
                    {{--                                </div>--}}
                    {{--                            </div>--}}

                    {{--                            <div class="row mb-3">--}}
                    {{--                                <div class="col-md-6">--}}
                    {{--                                    <label for="edit_shipping_cost" class="form-label">Shipping Cost:</label>--}}
                    {{--                                    <input type="number" class="form-control" id="edit_shipping_cost"--}}
                    {{--                                        name="shipping_cost" value="0" min="0" step="0.01">--}}
                    {{--                                </div>--}}
                    {{--                                <div class="col-md-6">--}}
                    {{--                                    <label for="edit_expected_delivery_date" class="form-label">Expected Delivery--}}
                    {{--                                        Date:</label>--}}
                    {{--                                    <input type="date" class="form-control" id="edit_expected_delivery_date"--}}
                    {{--                                        name="expected_delivery_date">--}}
                    {{--                                </div>--}}
                    {{--                            </div>--}}
                    {{--                        </div>--}}
                    {{--                    </div>--}}

                    <!-- Products Section -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">Products</h6>
                        </div>
                        <div class="card-body">
                            <div class="product-search-container mb-3">
                                <label for="editSearchProduct" class="form-label">Search Products</label>
                                <select class="product-search w-100" id="editSearchProduct" multiple="multiple">
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}"
                                                data-price="{{ $product->default_sell_price }}">
                                            {{ $product->name }} ({{ $product->sku }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="editProductTable" class="table-responsive" style="display: none;">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Product Name</th>
                                        <th>sell Quantity</th>

                                        <th>Unit Cost</th>


                                        <th>Unit Cost (Before Tax)</th>
                                        <th>Subtotal (Before Tax)</th>
                                        <th>Product Tax %</th>
                                        <th>Net Cost</th>
                                        <th>Line Total</th>

                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody id="editProductTableBody"></tbody>
                                    <tfoot>
                                    <tr>
                                        <td colspan="6" class="text-end">Total Before Tax:</td>
                                        <td colspan="2">
                                            <input type="number" class="form-control" id="edit_total_before_tax"
                                                   name="total_before_tax" readonly>
                                        </td>
                                        <td colspan="2">Total Tax:
                                            <input type="number" class="form-control" id="edit_tax_amount"
                                                   name="tax_amount" readonly>
                                        </td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Information -->
                    <!-- Payment Information -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">Payment Details</h6>
                        </div>
                        <div class="card-body">


                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="edit_payment_method" class="form-label">Payment Method:</label>
                                    <select class="form-select" id="edit_payment_method" name="payment_method">
                                        <option value="cash">Cash</option>
                                       <!-- <option value="bKash" >bKash</option>
                                      	<option value="nagad" >Nagad</option>
                                     	<option value="rocket" >Rocket</option> -->
                                        <option value="bank_transfer">Bank Transfer</option>
                                        <option value="cheque">Cheque</option>
                                        <option value="credit_card">Credit Card</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="edit_advance_balance" class="form-label">Advance Balance:</label>
                                    <input type="number" class="form-control" id="edit_advance_balance"
                                           name="advance_balance" value="0" min="0" step="0.01">
                                </div>
                                <div class="col-md-4">
                                    <label for="edit_payment_due" class="form-label">Payment Due:</label>
                                    <input type="number" class="form-control" id="edit_payment_due"
                                           name="payment_due" readonly>
                                </div>
                            </div>

                            <div class="row mb-3">

                                <div class="col-md-6">
                                    <label for="edit_discount_type" class="form-label">Discount Type:</label>
                                    <select class="form-select" id="edit_discount_type" name="discount_type">
                                        <option value="fixed">fixed</option>
                                        <option value="percentage">Percentage</option>


                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_discount_amount" class="form-label">Discount Amount:</label>
                                    <input type="number" class="form-control" id="edit_discount_amount"
                                           name="discount_amount" value="0" min="0" step="1">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="edit_net_total" class="form-label">Net Total:</label>
                                    <input type="number" class="form-control" id="edit_net_total"
                                           name="edit_net_total" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_payment_status" class="form-label">Payment Status:</label>
                                    <select class="form-select" id="edit_payment_status" name="payment_status">
                                        <option value="completed">Completed</option>
                                        <option value="pending">Pending</option>
                                        <option value="partial">Partial</option>
                                        <option value="overdue">Overdue</option>

                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>


                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="updatesell()">Update sell</button>
            </div>
        </div>
    </div>
</div>


@push('script')
    <script>
        function initializeProductSearch() {
            $('#searchProduct').select2({
                placeholder: 'Search Products by Name, SKU',
                allowClear: true,
                width: 'resolve',
                multiple: true,
                ajax: {
                    url: "{{ route($role . 'products.by-store') }}",
                    type: 'GET',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            store_id: $('#store_id').val(),
                            category_id: $('#category_id').val(), // Add category filter
                            search: params.term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.products.map(function (product) {
                                return {
                                    id: product.id,
                                    text: `${product.name} (${product.sku}) - (Quantity : ${product.quantity})`,
                                    price: product.price
                                };
                            })
                        };
                    },
                    cache: true
                },
                minimumInputLength: 0,
                templateResult: formatProduct,
                templateSelection: formatProductSelection
            });
        }
        // Update both store and category change events
        $('#store_id, #category_id').on('change', function() {
            const storeId = $('#store_id').val();

            if (storeId) {
                // Enable product search and reset
                $('#searchProduct')
                        .prop('disabled', false)
                        .val(null)
                        .trigger('change');

                // Reinitialize product search with current filters
                initializeProductSearch();
            } else {
                // Disable product search if no store selected
                $('#searchProduct')
                        .prop('disabled', true)
                        .val(null)
                        .trigger('change');
            }
        });

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
            $('#store_id').on('change', function () {
                const storeId = $(this).val();

                if (storeId) {
                    // Enable product search and reset
                    $('#searchProduct')
                        .prop('disabled', false)
                        .val(null)
                        .trigger('change');

                    // Reinitialize product search with current store context
                    initializeProductSearch();
                } else {
                    // Disable product search if no store selected
                    $('#searchProduct')
                        .prop('disabled', true)
                        .val(null)
                        .trigger('change');
                }
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
        });


        // Global variable to store current edit state
        let editProductCounter = 0;
        let editProductData = new Map();

        // Function to open edit modal and load sell data
        function openEditModal(editUrl) {
            // Reset previous state
            editProductCounter = 0;
            editProductData.clear();
            $('#editProductTableBody').empty();
            $('#editSearchProduct').val(null).trigger('change');

            // Show loading state
            showLoader();

            // Fetch sell data
            $.ajax({
                url: editUrl,
                method: 'GET',
                success: function (response) {
                    const sell = response.data;
                    populateEditForm(sell);
                    $('#editModal').modal('show');
                },
                error: function (xhr) {
                    hideLoader();
                    let response = xhr.responseText;
                    try {
                        response = JSON.parse(response);
                    } catch (e) {
                        response = {message: 'An error occurred'};
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
                    console.error('Error updating purchase:', response);
                }
            });
        }

        // Function to populate edit form with sell data
        function populateEditForm(sell) {
            // Set basic sell details


            $('#edit_sell_id').val(sell.id);
            $('#edit_customer_id').val(sell.customer_id).trigger('change');
            $('#edit_store_id').val(sell.store_id).trigger('change');
            $('#edit_payment_status').val(sell.payment_status).trigger('change');
            $('#edit_payment_due').val(sell.payment_due).trigger('change');

            $('#edit_reference_no').val(sell.reference_no);
            $('#edit_sell_date').val(formatDateTime(sell.sell_date));


            // $('#edit_store').val(sell.store_id).trigger('change');
            $('#edit_pay_term').val(sell.payment_term);
            $('#edit_pay_term_type').val(sell.payment_term_type).trigger('change');
            $('#edit_sell_status').val(sell.sell_status);
            $('#edit_store_id').val(sell.store_id);
            $('#pay_term_type').val(sell.payment_term_type);
            $('#pay_term').val(sell.payment_term);

            // Set shipping details


            if (sell.shipping_detail) {
                $('#edit_shipping_address').val(sell.shipping_detail.shipping_address || '');
                $('#edit_shipping_method').val(sell.shipping_detail.shipping_method || '');
                $('#edit_shipping_cost').val(sell.shipping_detail.shipping_cost || '');
                $('#edit_expected_delivery_date').val(formatDate(sell.shipping_detail.expected_delivery_date || ''));
            } else {
                $('#edit_shipping_address').val('');
                $('#edit_shipping_method').val('');
                $('#edit_shipping_cost').val('');
                $('#edit_expected_delivery_date').val('');
            }

            // Set payment details
            $('#edit_discount_type').val(sell.discount_type).trigger('change');
            $('#edit_discount_amount').val(sell.discount_amount);

            // Fix for payment method selection - get from the first payment if it exists
            if (sell.payments && sell.payments.length > 0) {
                $('#edit_payment_method').val(sell.payments[0].payment_method);
            }

            $('#edit_advance_balance').val(sell.advance_balance);
            $('#edit_payment_due').val(sell.payment_due);
            $('#edit_net_total').val(sell.net_total);

            // Set payment note from the first payment if it exists
            if (sell.payments && sell.payments.length > 0) {
                $('#edit_payment_note').val(sell.payments[0].payment_note);
            }

            $('#edit_additional_notes').val(sell.additional_notes);

            // Show product table
            $('#editProductTable').show();

            // Add products to table
            sell.items.forEach(item => {
                addProductToEditTable(item);
            });

            // Pre-select products in select2
            const productIds = sell.items.map(item => item.product_id);
            $('#editSearchProduct').val(productIds).trigger('change');


            // Update calculations
            updateEditTotals();

            // Display current document if exists
            if (sell.document_url) {
                const fileExtension = sell.document_url.split('.').pop().toLowerCase();
                let documentHtml = '';

                if (['jpg', 'jpeg', 'png'].includes(fileExtension)) {
                    documentHtml = `<img src="${sell.document_url}" class="img-thumbnail" style="max-height: 100px;">`;
                } else {
                    documentHtml =
                        `<a href="${sell.document_url}" target="_blank" class="btn btn-sm btn-info">View Current Document</a>`;
                }

                $('#edit_current_document').html(documentHtml);
            }

            // Show modal
            $('#editsellModal').modal('show');
            hideLoader();
        }

        $(document).ready(function () {

            $('#edit_advance_balance').on('input', function () {
                // Store the input value in a variable
                let advanceBalance = $(this).val();
                advanceBalance = parseFloat(advanceBalance) || 0;
                let netTotal = parseFloat($('#edit_net_total').val()) || 0;
                let paymentDue = netTotal - advanceBalance;
                $('#edit_payment_due').val(paymentDue.toFixed(2));


            });
        });

        $(document).ready(function () {
            updateEditTotals();
            $('#edit_discount_amount').on('input', function () {

                let discountType = $('#edit_discount_type').find(":selected").val();
                let discountAmount = parseFloat($('#edit_discount_amount').val()) || 0;
                let total_before_tax = parseFloat($('#edit_total_before_tax').val()) || 0;
                let total_tax = parseFloat($('#edit_tax_amount').val()) || 0;
                let total_net = total_before_tax + total_tax;

                if (discountType == 'percentage') {

                    netTotal = total_net - (total_net * discountAmount / 100);

                    $('#edit_net_total').val(netTotal.toFixed(2));
                    $('#edit_payment_due').val(netTotal.toFixed(2));
                    let payment_due = parseFloat($('#edit_net_total').val()) || 0;
                } else {

                    netTotal = total_net - discountAmount;

                    $('#edit_net_total').val(netTotal.toFixed(2));
                    $('#edit_payment_due').val(netTotal.toFixed(2));
                    let payment_due = parseFloat($('#edit_net_total').val()) || 0;

                }

            });
        });
        $(document).ready(function () {
            updateEditTotals();
            $('#edit_discount_type').on('input', function () {


                let discountType = $('#edit_discount_type').find(":selected").val();
                let discountAmount = parseFloat($('#edit_discount_amount').val()) || 0;
                let total_before_tax = parseFloat($('#edit_total_before_tax').val()) || 0;
                let total_tax = parseFloat($('#edit_tax_amount').val()) || 0;
                let total_net = total_before_tax + total_tax;

                if (discountType == 'percentage') {

                    netTotal = total_net - (total_net * discountAmount / 100);

                    $('#edit_net_total').val(netTotal.toFixed(2));
                } else {

                    netTotal = total_net - discountAmount;

                    $('#edit_net_total').val(netTotal.toFixed(2));

                }


            });
        });


        function addProductToEditTable(product) {

            $('#productTable').show();
            if (!editProductData.has(product.id)) {
                editProductCounter++;
                const price = $(product.element).data('price') || 0;

                const row = createEditProductRow(product);

                editProductData.set(product.id, {
                    element: row,
                    counter: editProductCounter
                });

                $('#editProductTableBody').append(row);
                updateEditCalculations(row);
            }
        }

        // Function to create product row in edit table
        function createEditProductRow(product) {



            const row = $('<tr>');
            row.attr('data-sku', product.product_id || product.id);

            row.html(`
            <td>${product.counter}</td>
            <td>${product.name}</td>
            <td>
            <input type="number" class="form-control quantity-input" value="${product.quantity}" min="0.01" step="0.01" required>

            </td>

            <td><input type="number" class="form-control unit-cost" value="${product.unit_cost_before_tax}" min="0" required></td>



            <td><input type="number" class="form-control unit-cost-after-discount" value="${product.unit_cost_before_tax}" readonly></td>
            <td class="subtotal">${product.unit_cost_before_tax * product.quantity}</td>
            <td><input type="number" class="form-control product-tax" value="${product.tax_amount}" min="0"></td>
            <td><input type="number" class="form-control net-cost" value="${product.net_cost}" readonly></td>
            <td><input type="number" class="form-control line-total" value="${product.net_cost * product.quantity}" readonly></td>

            <td><button type="button" class="btn btn-danger btn-sm delete-btn">×</button></td>
            `);

            // Bind input events
            row.find('input').on('input', function () {
                updateEditCalculations(row);
            });

            // Bind delete button
            row.find('.delete-btn').on('click', function () {
                removeProductFromEditTable(product.id);
            });

            return row;
        }

        // Function to update calculations for edit form
        function updateEditCalculations(row) {
            const quantity = parseFloat(row.find('.quantity-input').val()) || 0;
            const unitCost = parseFloat(row.find('.unit-cost').val()) || 0;
            const discount = parseFloat(row.find('.discount').val()) || 0;
            const tax = parseFloat(row.find('.product-tax').val()) || 0;


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


            updateEditTotals();
        }

        // Function to update totals in edit form
        function updateEditTotals() {
            let totalBeforeTax = 0;
            let totalTax = 0;
            let netTotal = 0;

            $('#editProductTableBody tr').each(function () {
                const row = $(this);
                totalBeforeTax += parseFloat(row.find('.subtotal').text()) || 0;
                totalTax += parseFloat(row.find('.product-tax').val() || 0) * parseFloat(row.find('.subtotal')
                    .text() || 0) / 100;
            });

            netTotal = totalBeforeTax + totalTax;

            // Update total fields
            $('#edit_total_before_tax').val(totalBeforeTax.toFixed(2));
            $('#edit_tax_amount').val(totalTax.toFixed(2));
            $('#edit_net_total').val(netTotal.toFixed(2));

            // Update payment due
            const advanceBalance = parseFloat($('#edit_advance_balance').val()) || 0;
            $('#edit_payment_due').val((netTotal - advanceBalance).toFixed(2));
        }

        // Function to remove product from edit table
        function removeProductFromEditTable(sku) {
            if (editProductData.has(sku)) {
                const data = editProductData.get(sku);
                data.element.remove();
                editProductData.delete(sku);
                updateEditRowNumbers();
                updateEditTotals();

                // Unselect from select2
                const currentSelections = $('#editSearchProduct').val().filter(id => id != sku);
                $('#editSearchProduct').val(currentSelections).trigger('change');
            }
        }

        // Function to update row numbers in edit table
        function updateEditRowNumbers() {
            let counter = 1;
            $('#editProductTableBody tr').each(function () {
                $(this).find('td:first').text(counter);
                counter++;
            });
        }

        // Function to submit updated sell data
        function updatesell() {
            // Create items array
            const items = [];
            $('#editProductTableBody tr').each(function () {
                const row = $(this);
                const productId = row.attr('data-sku');
                items.push({
                    product_id: parseInt(productId),
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

            // Get form data
            const formData = new FormData($('#editsellForm')[0]);
            const sellId = $('#edit_sell_id').val();

            // Create sell data object
            const sellData = {
                ...Object.fromEntries(formData),
                items: items
            };

            // Add shipping details if present
            if ($('#edit_shipping_address').val()) {
                sellData.shipping_address = $('#edit_shipping_address').val();
                sellData.shipping_method = $('#edit_shipping_method').val();
                sellData.shipping_cost = parseFloat($('#edit_shipping_cost').val()) || 0;
                sellData.expected_delivery_date = $('#edit_expected_delivery_date').val();
            }

            // Add payment details if advance balance exists
            const advanceBalance = parseFloat($('#edit_advance_balance').val() || 0);
            if (advanceBalance > 0) {
                sellData.advance_balance = advanceBalance;
                sellData.payment_method = $('#edit_payment_method').val();
                sellData.payment_note = $('#edit_payment_note').val();
            }

            // Show loading state
            showLoader();

            // Submit update request
            const editsellId = $('#edit_sell_id').val();
            const updateUrl = "{{ route($role . 'sell.update', ':id') }}".replace(':id', editsellId);

            $.ajax({
                url: updateUrl,
                method: 'PUT',
                data: JSON.stringify(sellData),
                processData: false,
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    hideLoader();
                    $('#editModal').modal('hide');
                    loadTable(); // Refresh the main sell table
                    AjaxNotifications.success('sell updated successfully');
                },
                error: function (xhr) {
                    hideLoader();
                    let response = xhr.responseText;
                    try {
                        response = JSON.parse(response);
                    } catch (e) {
                        response = {message: 'An error occurred'};
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
                    console.error('Error updating purchase:', response);
                }
            });
        }

        // Helper function to format date
        function formatDate(dateString) {
            if (!dateString) return '';
            return new Date(dateString).toISOString().split('T')[0];
        }

        // Helper function to format datetime
        function formatDateTime(dateString) {
            if (!dateString) return '';
            return new Date(dateString).toISOString().slice(0, 16);
        }

        $(document).ready(function () {
            $('#editSearchProduct').select2({
                placeholder: 'Enter Product name / SKU / Scan bar code',
                allowClear: true,
                width: 'resolve',
                multiple: true
            }).on('select2:select', function (e) {
                const option = $(e.params.data.element);
                const productData = {
                    id: e.params.data.id,
                    name: e.params.data.text,
                    price: option.data('price') || 0
                };
                addProductToEditTable({
                    product_id: productData.id,
                    product_name: productData.name,
                    quantity: 1,
                    unit_cost: productData.price,
                    discount_percent: 0,
                    unit_cost_before_tax: productData.price,
                    tax_amount: 0,
                    net_cost: productData.price,
                    profit_margin: 0,
                    unit_selling_price: productData.price
                });
            }).on('select2:unselect', function (e) {
                removeProductFromEditTable(e.params.data.id);
            });

            $('#edit_customer_id').select2({
                width: 'resolve'
            });

            // $('#edit_sell_date').flatpickr({
            //     enableTime: true,
            //     dateFormat: "Y-m-d H:i",
            // });
            //
            // $('#edit_expected_delivery_date').flatpickr({
            //     dateFormat: "Y-m-d",
            // });

        });
    </script>
@endpush
