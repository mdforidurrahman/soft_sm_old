<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Purchase</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editPurchaseForm">
                    @csrf
                    <input type="hidden" name="purchase_id" id="edit_purchase_id">

                    <!-- Basic Purchase Information -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">Purchase Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="edit_supplier_id" class="form-label">Supplier*</label>
                                    <div class="input-group">
                                        <select name="supplier_id" class="form-select select2" id="edit_supplier_id"
                                                required>
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
                                <div class="col-md-6">
                                    <label for="edit_reference_no" class="form-label">Reference No:</label>
                                    <input type="text" class="form-control" id="edit_reference_no"
                                           name="reference_no">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="edit_purchase_date" class="form-label">Purchase Date*</label>
                                    <input type="datetime-local" class="form-control" id="edit_purchase_date"
                                           name="purchase_date" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_purchase_status" class="form-label">Purchase Status*</label>
                                    <select class="form-select" id="edit_purchase_status" name="purchase_status"
                                            required>
                                        <option value="">Please Select</option>
                                        <option value="returned">Returned</option>
                                        <option value="pending">Pending</option>
                                        <option value="completed">Completed</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="edit_store" class="form-label">Store Name*</label>
                                    <select class="form-select" id="edit_store" name="store_id" required>
                                        <option value="">Please Select Store</option>
                                        @foreach ($storeName as $item)
                                            <option value="{{ $item->id }}">
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- <div class="col-md-6">
                                    <label for="edit_payment_term" class="form-label">Pay term:</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="edit_payment_term"
                                            name="payment_term">
                                        <select class="form-select" id="edit_payment_term_type"
                                            name="payment_term_type">
                                            <option value="">Please Select</option>
                                            <option value="days">Days</option>
                                            <option value="months">Months</option>
                                        </select>
                                    </div>
                                </div> -->
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Information -->
                  <!--    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">Shipping Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="edit_shipping_address" class="form-label">Shipping Address:</label>
                                    <input type="text" class="form-control" id="edit_shipping_address"
                                           name="shipping_address">
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_shipping_method" class="form-label">Shipping Method:</label>
                                    <input type="text" class="form-control" id="edit_shipping_method"
                                           name="shipping_method">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="edit_shipping_cost" class="form-label">Shipping Cost:</label>
                                    <input type="number" class="form-control" id="edit_shipping_cost"
                                           name="shipping_cost" value="0" min="0" step="0.01">
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_expected_delivery_date" class="form-label">Expected Delivery
                                        Date:</label>
                                    <input type="date" class="form-control" id="edit_expected_delivery_date"
                                           name="expected_delivery_date">
                                </div>
                            </div>
                        </div>
                    </div> -->

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
                                                data-price="{{ $product->default_purchase_price }}">
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
                                    <tbody id="editProductTableBody"></tbody>
                                    <tfoot>
                                    <tr>
                                        <td colspan="6" class="text-end">Total Before Tax:</td>
                                        <td colspan="2">
                                            <input type="number" class="form-control" id="edit_total_before_tax"
                                                   name="total_before_tax" readonly>
                                        </td>
                                        <td colspan="2">Total Tax:</td>
                                        <td colspan="3">
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
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">Payment Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="edit_discount_type" class="form-label">Discount Type:</label>
                                    <select class="form-select" id="edit_discount_type" name="discount_type">
                                        <option value="">None</option>
                                        <option value="fixed">Fixed Amount</option>
                                        <option value="percentage">Percentage</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="edit_discount_amount" class="form-label">Discount Amount:</label>
                                    <input type="number" class="form-control" id="edit_discount_amount"
                                           name="discount_amount" value="0" min="0" step="0.01">
                                </div>
                                <div class="col-md-4">
                                    <label for="edit_net_total" class="form-label">Net Total:</label>
                                    <input type="number" class="form-control" id="edit_net_total" name="net_total"
                                           readonly>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="edit_payment_method" class="form-label">Payment Method:</label>
                                    <select class="form-select" id="edit_payment_method" name="payment_method">
                                        <option value="cash">Cash</option>
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
                                    <label for="edit_payment_note" class="form-label">Payment Note:</label>
                                    <textarea class="form-control" id="edit_payment_note" name="payment_note"
                                              rows="3"></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_additional_notes" class="form-label">Additional Notes:</label>
                                    <textarea class="form-control" id="edit_additional_notes" name="additional_notes"
                                              rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="updatePurchase()">Update Purchase</button>
            </div>
        </div>
    </div>
</div>


@push('script')
    <script>
        let editProductCounter = 0;
        let editProductData = new Map();




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


        function initializeProductSearch() {
            $('#editSearchProduct').select2({
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
                            store_id: $('#edit_store').val(), // Use the edit store selector
                            search: params.term
                        };
                    },
                    processResults: function (data) {
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
                    cache: true
                },
                minimumInputLength: 0,
                templateResult: formatProduct,
                templateSelection: formatProductSelection
            });
        }

        $(document).ready(function () {
            // Store selection triggers product search initialization


            $('#edit_store').on('change', function () {
                const storeId = $(this).val();

                // Clear all products from the table
                $('#editProductTableBody').empty();
                editProductData.clear();

                // Reset product search dropdown
                $('#editSearchProduct')
                    .val(null)
                    .trigger('change');

                if (storeId) {
                    // Enable product search
                    $('#editSearchProduct').prop('disabled', false);

                    // Reinitialize product search with current store context
                    initializeProductSearch();

                    // Reset totals
                    updateEditTotals();
                } else {
                    // Disable product search if no store selected
                    $('#editSearchProduct')
                        .prop('disabled', true)
                        .val(null)
                        .trigger('change');
                }
            });


            // Initial setup
            initializeSelects();
            initializeEventListeners();
        });



        // Function to open edit modal and load purchase data
        function openEditModal(editUrl) {
            // Reset previous state
            editProductCounter = 0;
            editProductData.clear();
            $('#editProductTableBody').empty();
            $('#editSearchProduct').val(null).trigger('change');

            // Show loading state
            showLoader();

            // Fetch purchase data
            $.ajax({
                url: editUrl,
                method: 'GET',
                success: function (response) {
                    const purchase = response.data;
                    populateEditForm(purchase);

                    console.log(purchase);


                    $('#editModal').modal('show');
                },
                error: function(xhr) {
                    hideLoader();
                    let response = xhr.responseText;
                    try {
                        response = JSON.parse(response);
                    } catch (e) {
                        response = { message: 'An error occurred' };
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

        function populateEditForm(purchase) {

            console.log('Populating edit form with data:', purchase);


            $('#edit_purchase_id').val(purchase.id);

            $('#edit_supplier_id').val(purchase.supplier_id).trigger('change');
            // $(`#edit_supplier_id option[value="${purchase.supplier_id}"]`).prop('selected', true);


            $('#edit_reference_no').val(purchase.reference_no);
            $('#edit_purchase_date').val(formatDateTime(purchase.purchase_date));


            $('#edit_store').val(purchase.store_id).trigger('change');
            $('#edit_payment_term').val(purchase.payment_term);
            $('#edit_payment_term_type').val(purchase.payment_term_type).trigger('change');
            $('#edit_purchase_status').val(purchase.purchase_status);

            // Set shipping details
            $('#edit_shipping_address').val(purchase.shipping_detail.shipping_address);
            $('#edit_shipping_method').val(purchase.shipping_detail.shipping_method);
            $('#edit_shipping_cost').val(purchase.shipping_detail.shipping_cost);
            $('#edit_expected_delivery_date').val(formatDate(purchase.shipping_detail.expected_delivery_date));


            // Set payment details
            $('#edit_discount_type').val(purchase.discount_type).trigger('change');
            $('#edit_discount_amount').val(purchase.discount_amount);

            // Fix for payment method selection - get from the first payment if it exists
            if (purchase.payments && purchase.payments.length > 0) {
                $('#edit_payment_method').val(purchase.payments[0].payment_method);
            }

            $('#edit_advance_balance').val(purchase.advance_balance);

            // Set payment note from the first payment if it exists
            if (purchase.payments && purchase.payments.length > 0) {
                $('#edit_payment_note').val(purchase.payments[0].payment_note);
            }

            $('#edit_additional_notes').val(purchase.additional_notes);

            // Show product table
            $('#editProductTable').show();

            // Add products to table
            purchase.items.forEach(item => {
                addProductToEditTable(item);
            });

            // Pre-select products in select2
            const productIds = purchase.items.map(item => item.product_id);
            $('#editSearchProduct').val(productIds).trigger('change');

            // Update calculations
            updateEditTotals();

            // Display current document if exists
            if (purchase.document_url) {
                const fileExtension = purchase.document_url.split('.').pop().toLowerCase();
                let documentHtml = '';

                if (['jpg', 'jpeg', 'png'].includes(fileExtension)) {
                    documentHtml = `<img src="${purchase.document_url}" class="img-thumbnail" style="max-height: 100px;">`;
                } else {
                    documentHtml =
                        `<a href="${purchase.document_url}" target="_blank" class="btn btn-sm btn-info">View Current Document</a>`;
                }

                $('#edit_current_document').html(documentHtml);
            }

            // Show modal
            $('#editPurchaseModal').modal('show');
            hideLoader();
        }

        // Function to add product to edit table
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
            <td>${product.id}</td>
            <td>${product.product_name}</td>
            <td>
            <input type="number" class="form-control quantity-input" value="${product.quantity}" min="0.01" step="0.01" required>
            <select class="form-select mt-1">
                <option selected>Pieces</option>
                <option>Dozen</option>
                <option>Box</option>
            </select>
            </td>
            <td><input type="number" class="form-control unit-cost" value="${product.unit_cost}" min="0" required></td>
            <td><input type="number" class="form-control discount" value="${product.discount_percent}" min="0" max="100"></td>
            <td><input type="number" class="form-control unit-cost-after-discount" value="${product.unit_cost_before_tax}" readonly></td>
            <td class="subtotal">${product.unit_cost_before_tax * product.quantity}</td>
            <td><input type="number" class="form-control product-tax" value="${product.tax_amount}" min="0"></td>
            <td><input type="number" class="form-control net-cost" value="${product.net_cost}" readonly></td>
            <td><input type="number" class="form-control line-total" value="${product.net_cost * product.quantity}" readonly></td>
            <td><input type="number" class="form-control profit-margin" value="${product.profit_margin}" min="0" max="100"></td>
            <td><input type="number" class="form-control selling-price" value="${product.unit_selling_price}" min="0" required></td>
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

        // Function to submit updated purchase data
        function updatePurchase() {
            // Create items array with correct product_id mapping
            const items = [];
            $('#editProductTableBody tr').each(function () {
                const row = $(this);
                const productId = row.attr('data-sku'); // Get the product ID from data attribute

                items.push({
                    product_id: parseInt(productId), // Ensure it's an integer
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
            const formData = new FormData($('#editPurchaseForm')[0]);

            // Create purchase data object with all required fields
            const purchaseData = {
                supplier_id: parseInt($('#edit_supplier_id').val()),
                store_id: parseInt($('#edit_store').val()),
                reference_no: $('#edit_reference_no').val(),
                purchase_date: $('#edit_purchase_date').val(),
                purchase_status: $('#edit_purchase_status').val(),
                payment_term: $('#edit_payment_term').val(),
                payment_term_type: $('#edit_payment_term_type').val(),
                shipping_address: $('#edit_shipping_address').val(),
                shipping_method: $('#edit_shipping_method').val(),
                shipping_cost: parseFloat($('#edit_shipping_cost').val()) || 0,
                expected_delivery_date: $('#edit_expected_delivery_date').val(),
                discount_type: $('#edit_discount_type').val(),
                discount_amount: parseFloat($('#edit_discount_amount').val()) || 0,
                advance_balance: parseFloat($('#edit_advance_balance').val()) || 0,
                payment_method: $('#edit_payment_method').val(),
                payment_note: $('#edit_payment_note').val(),
                additional_notes: $('#edit_additional_notes').val(),
                items: items
            };

            // Show loading state
            showLoader();

            // Submit update request
            const editPurchaseId = $('#edit_purchase_id').val();
            const updateUrl = "{{ route($role . 'purchase.update', ':id') }}".replace(':id', editPurchaseId);

            $.ajax({
                url: updateUrl,
                method: 'PUT',
                data: JSON.stringify(purchaseData),
                processData: false,
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    hideLoader();
                    $('#editModal').modal('hide');
                    loadTable();
                    AjaxNotifications.success('Purchase updated successfully');
                },
                error: function(xhr) {
                    hideLoader();
                    let response = xhr.responseText;
                    try {
                        response = JSON.parse(response);
                    } catch (e) {
                        response = { message: 'An error occurred' };
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

        // $('#edit_supplier_id, #edit_store_id').select2({
        //     width: 'resolve'
        // });


        $('#edit_purchase_date').flatpickr({
            enableTime: true,
            dateFormat: "Y-m-d H:i",
        });

        $('#edit_expected_delivery_date').flatpickr({
            dateFormat: "Y-m-d",
        });

        function initializeSelects() {
            $('.product-search').select2({
                placeholder: 'Enter Product name / SKU / Scan bar code',
                allowClear: true,
                width: 'resolve',
                multiple: true
            });

            $('#supplier_id, #purchase_status, #business_location, #pay_term_type').select2({
                width: 'resolve'
            });
        }

        function initializeEventListeners() {
            // Product selection
            $('#searchProduct').on('select2:select', function(e) {
                addProductToTable(e.params.data);
            }).on('select2:unselect', function(e) {
                removeProductFromTable(e.params.data.id);
            });

            // Form submission
            $('#purchaseForm').on('submit', function(e) {
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
    </script>
@endpush
