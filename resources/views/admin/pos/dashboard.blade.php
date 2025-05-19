@extends('layouts.admin')
@section('title', 'POS Dashboard')

@section('content')
    <!-- Include CSS and JS libraries -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <style>
        /* Your existing CSS styles */
    </style>

    <div class="container pos-dashboard">
        <div class="row">
            <!-- Cart Section (Left) -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5><i class="fas fa-shopping-cart"></i> Cart</h5>
                        <select class="form-control me-2" id="store-select" required style="width: 15vw;">
                            <option value="">Select Store</option>
                            @foreach ($stores as $store)
                                <option value="{{ $store->name }}">{{ $store->name }}</option>
                            @endforeach
                        </select>

                        <button class="btn btn-outline-danger btn-sm" id="clear-cart">
                            <i class="fas fa-trash-alt"></i> Clear Cart
                        </button>
                    </div>
                    <div class="card-body overflow-auto" style="max-height: 400px;">
                        <table class="table table-bordered" id="cart-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Customer</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                    <th>Remove</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Product Section (Right) -->
            <div class="col-md-6">
                <div class="d-flex justify-content-between mb-3">
                    <select class="form-control me-2" id="customer-select" required>
                        <option value="">Walk-In Customer</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                    <select class="form-control me-2" id="category-select">
                        <option value="">All Categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-box-open"></i> Products</h5>
                    </div>
                    <div class="card-body overflow-auto" style="max-height: 400px;">
                        <div id="productResults" class="row">
                            <!-- Products will be loaded here dynamically -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Section -->
        <div class="card-footer" style="background-color: #f8f9fa; border-top: 2px solid #dee2e6;">
            <div class="summary d-flex align-items-center justify-content-between mb-3 flex-wrap">
                <div><strong>Items:</strong> <span id="total-items">0</span></div>
                <div><strong>Total:</strong> $<span id="total-amount">0.00</span></div>

                <!-- Tax input -->
                <div class="d-flex align-items-center">
                    <i class="fas fa-percentage me-1"></i>
                    <label for="tax-input" class="me-2">Tax (%):</label>
                    <input type="number" id="tax-input" class="form-control d-inline-block live-update"
                        style="width: 80px;" value="0" />
                </div>

                <!-- Shipping input -->
                <div class="d-flex align-items-center">
                    <i class="fas fa-shipping-fast me-1"></i>
                    <label for="shipping-input" class="me-2">Shipping ($):</label>
                    <input type="number" id="shipping-input" class="form-control d-inline-block live-update"
                        style="width: 80px;" value="0" />
                </div>

                <!-- Discount input -->
                <div class="d-flex align-items-center">
                    <i class="fas fa-tags me-1"></i>
                    <label for="discount-input" class="me-2">Discount (%):</label>
                    <input type="number" id="discount-input" class="form-control d-inline-block live-update"
                        style="width: 80px;" value="0" />
                </div>

                <!-- Shipping Address -->
                <div class="d-flex align-items-center">
                    <label for="shipping-address" class="me-2">Shipping Address:</label>
                    <input type="text" id="shipping-address" class="form-control" placeholder="Enter shipping address" />
                </div>

                <!-- Total Payable -->
                <h4 class="m-0"><strong>Total Payable:</strong> <span id="payable-amount"
                        style="font-weight: bold; color: #28a745;">$0.00</span></h4>
            </div>

            <form id="order-form" method="POST">
                @csrf
                <input type="hidden" name="contact_id" id="contact-id-input" value="">
                <input type="hidden" name="location" id="store-name-input" value="">
                <input type="hidden" name="transaction_date" id="transaction-date-input" value="{{ now() }}">
                <input type="hidden" name="subtotal" id="subtotal-input" value="">
                <input type="hidden" name="discount" id="discount-value-input" value="">
                <input type="hidden" name="order_tax" id="tax-value-input" value="">
                <input type="hidden" name="shipping_cost" id="shipping-cost-input" value="">
                <input type="hidden" name="total" id="total-value-input" value="">
                <input type="hidden" name="payment_method" id="payment-method-input" value="">
                <input type="hidden" name="transaction_status" value="completed">
                <input type="hidden" name="items" id="items-input" value="">
                <input type="hidden" name="invoiceNo" id="invoiceNo-input" value="">
                <!-- New Field for Invoice Number -->
                <input type="hidden" name="shippingAddress" id="shippingAddress-input" value="">
                <!-- Corrected Field Name -->

                <button type="button" class="btn btn-success payment-btn" data-method="Cash">Cash</button>
                <button type="button" class="btn btn-primary payment-btn" data-method="Card">Card</button>
            </form>
        </div>

        <!-- Modal for Receipt -->
        <div class="modal fade" id="receiptModal" tabindex="-1" aria-labelledby="receiptModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-body">
                        <div id="receipt-content"
                            style="font-family: Arial, sans-serif; line-height: 1.6; max-width: 90%; margin: auto;">
                            <!-- Shop Header -->
                            <div style="text-align: center; margin-bottom: 20px;">
                                <h3 id="receipt-store"></h3>
                                <p>UK</p>
                                <p>GSTIN: 3412569900</p>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <div>
                                    <p><strong>Invoice No.</strong> <span id="invoice-number"></span></p>
                                    <p><strong>Customer:</strong> <span id="receipt-customer"></span></p>
                                    <p><strong>Shipping Address:</strong> <span id="receipt-shipping-address"></span></p>
                                </div>
                                <div>
                                    <p><strong>Date:</strong> <span id="receipt-date"></span></p>
                                    <p><strong>Payment Method:</strong> <span id="payment-method"></span></p>
                                </div>
                            </div>
                            <!-- Product Table -->
                            <table class="table table-bordered" style="width: 100%; text-align: left;">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Unit Price</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody id="receipt-items"></tbody>
                            </table>
                            <hr>
                            <!-- Summary Section -->
                            <div class="receipt-summary">
                                <table>
                                    <tr>
                                        <td>Subtotal:</td>
                                        <td>$<span id="subtotal-amount"></span></td>
                                    </tr>
                                    <tr>
                                        <td>Discount (<span id="discount-percent"></span>%):</td>
                                        <td>($<span id="discount-amount"></span>)</td>
                                    </tr>
                                    <tr>
                                        <td>Tax (<span id="tax-percent"></span>%):</td>
                                        <td>$<span id="tax-amount"></span></td>
                                    </tr>
                                    <tr class="shipping-row">
                                        <td>Shipping:</td>
                                        <td>$<span id="shipping-amount"></span></td>
                                    </tr>
                                    <tr class="extra-spacing-row">
                                        <td colspan="2"></td>
                                    </tr>
                                    <tr style="border-top: 2px solid black;">
                                        <td>Total Payable:</td>
                                        <td>$<span id="receipt-total-amount"></span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="print-receipt">Print Receipt</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- JavaScript Code -->
        <script>
            $(document).ready(function() {
                const cartTableBody = $('#cart-table tbody');
                let selectedCustomer = '';
                let selectedStore = '';
                let paymentMethod = '';
                const invoiceNumber = `INV${new Date().getTime()}`;

                // Function to show a custom confirmation toast
                function showConfirmationToast(message, callback) {
                    const toast = document.createElement('div');
                    toast.style.position = 'fixed';
                    toast.style.top = '50%';
                    toast.style.left = '50%';
                    toast.style.transform = 'translate(-50%, -50%)';
                    toast.style.backgroundColor = 'white';
                    toast.style.color = '#ff7f00';
                    toast.style.padding = '20px';
                    toast.style.border = '2px solid #ff7f00';
                    toast.style.borderRadius = '10px';
                    toast.style.boxShadow = '0 0 15px rgba(0, 0, 0, 0.3)';
                    toast.style.zIndex = '10000';
                    toast.style.textAlign = 'center';

                    toast.innerHTML = `
                    <p>${message}</p>
                    <button id="confirm-btn" style="background-color: #ff7f00; color: white; border: none; padding: 10px 20px; cursor: pointer; margin-right: 10px;">Confirm</button>
                    <button id="cancel-btn" style="background-color: #ddd; color: black; border: none; padding: 10px 20px; cursor: pointer;">Cancel</button>
                `;

                    document.body.appendChild(toast);

                    document.getElementById('confirm-btn').onclick = function() {
                        document.body.removeChild(toast);
                        if (callback) callback(true);
                    };
                    document.getElementById('cancel-btn').onclick = function() {
                        document.body.removeChild(toast);
                        if (callback) callback(false);
                    };
                }

                // Clear Cart functionality
                $('#clear-cart').on('click', function() {
                    cartTableBody.empty();
                    updateCartSummary();
                });

                // Update customer name and set contact_id
                $('#customer-select').on('change', function() {
                    selectedCustomer = $(this).find('option:selected').text();
                    $('.cart-customer').text(selectedCustomer);

                    const contactId = $(this).val();
                    if (contactId) {
                        $('#contact-id-input').val(contactId);
                    } else {
                        $('#contact-id-input').val('');
                    }
                });
                $('#store-select').on('change', function() {
                    selectedStore = $(this).find('option:selected').text();
                    $('.cart-store').text(selectedStore);

                    const storeName = $(this).val();
                    if (storeName) {
                        $('#store-name-input').val(storeName);
                    } else {
                        $('#store-name-input').val('');
                    }
                });

                // Ensure customer is selected before confirming order
                function validateCustomerSelection() {
                    if (!$('#contact-id-input').val()) {
                        toastr.error('Please select a customer before proceeding.');
                        return false;
                    }
                    return true;
                }

                function validateCustomerSelection() {
                    if (!$('#store-name-input').val()) {
                        toastr.error('Please select a store before proceeding.');
                        return false;
                    }
                    return true;
                }

                // Update cart summary and calculate values
                function updateCartSummary() {
                    let totalItems = 0,
                        totalAmount = 0;
                    cartTableBody.find('tr').each(function() {
                        const quantity = parseInt($(this).find('.quantity-input').val());
                        const subtotal = parseFloat($(this).find('.subtotal').text().slice(1));
                        totalItems += quantity;
                        totalAmount += subtotal;
                    });
                    $('#total-items').text(totalItems);
                    $('#total-amount').text(totalAmount.toFixed(2));
                    updatePayableAmount();
                }

                function updatePayableAmount() {
                    const totalAmount = parseFloat($('#total-amount').text());
                    const tax = parseFloat($('#tax-input').val()) || 0;
                    const shipping = parseFloat($('#shipping-input').val()) || 0;
                    const discount = parseFloat($('#discount-input').val()) || 0;
                    const payableAmount = totalAmount + (totalAmount * tax / 100) + shipping - (totalAmount * discount /
                        100);
                    $('#payable-amount').text(`$${payableAmount.toFixed(2)}`);

                    $('#subtotal-input').val(totalAmount.toFixed(2));
                    $('#tax-value-input').val(tax);
                    $('#shipping-cost-input').val(shipping.toFixed(2));
                    $('#discount-value-input').val(discount);
                    $('#total-value-input').val(payableAmount.toFixed(2));
                }

                $('.live-update').on('input', function() {
                    updatePayableAmount();
                });

                $('.payment-btn').on('click', function() {
                    if (!validateCustomerSelection()) return; // Ensure a customer is selected

                    paymentMethod = $(this).data('method');
                    $('#payment-method-input').val(paymentMethod);
                    // Generate a unique invoice number

                    $('#invoiceNo-input').val(invoiceNumber); // Set the invoice number

                    // Set the shipping address
                    const shippingAddress = $('#shipping-address').val();
                    $('#shippingAddress-input').val(
                        shippingAddress); // Corrected to match the hidden input field

                    showConfirmationToast('Are you sure you want to place this order?', function(confirmed) {
                        if (confirmed) {
                            generateReceipt(); // Generate the receipt for display

                            // Collect all items in JSON format
                            const items = [];
                            cartTableBody.find('tr').each(function() {
                                const quantity = parseInt($(this).find('.quantity-input')
                                    .val());
                                const unitSellingPrice = parseFloat($(this).find('.subtotal')
                                    .text().slice(1)) / quantity;
                                const discountPercent = parseFloat($('#discount-input')
                                    .val()) || 0;
                                const unitCostBeforeTax = unitSellingPrice - (unitSellingPrice *
                                    discountPercent / 100);
                                const taxRate = parseFloat($('#tax-input').val()) || 0;
                                const taxAmount = (unitCostBeforeTax * taxRate / 100).toFixed(
                                    2);
                                const netCost = (unitCostBeforeTax + parseFloat(taxAmount))
                                    .toFixed(2);
                                const profitMargin = ((unitSellingPrice - netCost) /
                                    unitSellingPrice * 100).toFixed(2);

                                items.push({
                                    product_id: $(this).data('id'),
                                    quantity: quantity,
                                    unit_cost: unitSellingPrice.toFixed(2),
                                    discount_percent: discountPercent,
                                    unit_cost_before_tax: unitCostBeforeTax.toFixed(2),
                                    tax_amount: parseFloat(taxAmount),
                                    net_cost: parseFloat(netCost),
                                    profit_margin: parseFloat(profitMargin),
                                    unit_selling_price: unitSellingPrice.toFixed(2)
                                });
                            });
                            $('#items-input').val(JSON.stringify(items));

                            // Update tax, discount, and shipping cost values
                            $('#tax-value-input').val(parseFloat($('#tax-input').val()) || 0);
                            $('#discount-value-input').val(parseFloat($('#discount-input').val()) || 0);
                            $('#shipping-cost-input').val(parseFloat($('#shipping-input').val()) || 0);

                            // Send AJAX request to save transaction
                            $.ajax({
                                type: 'POST',
                                url: '{{ route($role . 'pos.store') }}',
                                data: $('#order-form').serialize(),
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(response) {
                                    toastr.success(response.message);
                                },
                                error: function(error) {
                                    toastr.error('Something went wrong.');
                                    console.error('Error:', error);
                                }
                            });
                        }
                    });
                });




                $('#print-receipt').on('click', function() {
                    const {
                        jsPDF
                    } = window.jspdf;
                    const doc = new jsPDF({
                        orientation: 'portrait',
                        unit: 'pt',
                        format: 'a4'
                    });

                    doc.html(document.getElementById('receipt-content'), {
                        callback: function(doc) {
                            const blob = doc.output('blob');
                            const blobUrl = URL.createObjectURL(blob);
                            window.open(blobUrl, '_blank');
                        },
                        x: 20,
                        y: 20,
                        width: 550,
                        html2canvas: {
                            scale: 0.80
                        }
                    });
                });

                function addToCart() {
                    const productId = $(this).data('id');
                    const productName = $(this).data('name');
                    const productPrice = $(this).data('price');
                    const existingRow = cartTableBody.find(`tr[data-id="${productId}"]`);

                    if (existingRow.length) {
                        const quantityInput = existingRow.find('.quantity-input');
                        const newQuantity = parseInt(quantityInput.val()) + 1;
                        quantityInput.val(newQuantity);
                        const newSubtotal = (productPrice * newQuantity).toFixed(2);
                        existingRow.find('.subtotal').text(`$${newSubtotal}`);
                    } else {
                        cartTableBody.append(`
                        <tr data-id="${productId}">
                            <td>${productName}</td>
                            <td class="cart-customer">${selectedCustomer}</td>
                            <td><input type="number" value="1" class="form-control quantity-input" min="1" /></td>
                            <td class="subtotal">$${productPrice}</td>
                            <td><span class="remove-item">&times;</span></td>
                        </tr>
                    `);
                    }
                    updateCartSummary();
                }

                $('#cart-table').on('click', '.remove-item', function() {
                    $(this).closest('tr').remove();
                    updateCartSummary();
                });

                function generateReceipt() {
                    const date = new Date().toLocaleString();

                    $('#receipt-date').text(date);
                    $('#invoice-number').text(invoiceNumber);
                    $('#receipt-customer').text(selectedCustomer);
                    $('#receipt-store').text(selectedStore);
                    $('#receipt-shipping-address').text($('#shipping-address').val());
                    $('#payment-method').text(paymentMethod);

                    const receiptItems = [];
                    let subtotal = 0;

                    cartTableBody.find('tr').each(function() {
                        const itemName = $(this).find('td:eq(0)').text();
                        const itemQuantity = $(this).find('.quantity-input').val();
                        const itemPrice = parseFloat($(this).find('.subtotal').text().slice(1)) / itemQuantity;
                        const itemSubtotal = parseFloat($(this).find('.subtotal').text().slice(1));
                        subtotal += itemSubtotal;

                        receiptItems.push({
                            name: itemName,
                            quantity: itemQuantity,
                            price: itemPrice.toFixed(2),
                            subtotal: itemSubtotal.toFixed(2)
                        });
                    });

                    $('#receipt-items').empty();
                    receiptItems.forEach(item => {
                        $('#receipt-items').append(`
                <tr>
                    <td>${item.name}</td>
                    <td>${item.quantity}</td>
                    <td>$${item.price}</td>
                    <td>$${item.subtotal}</td>
                </tr>
            `);
                    });

                    const taxPercent = parseFloat($('#tax-input').val()) || 0;
                    const shipping = parseFloat($('#shipping-input').val()) || 0;
                    const discountPercent = parseFloat($('#discount-input').val()) || 0;
                    const taxAmount = (subtotal * taxPercent / 100).toFixed(2);
                    const discountAmount = (subtotal * discountPercent / 100).toFixed(2);
                    const totalPayable = (subtotal + parseFloat(taxAmount) + shipping - parseFloat(discountAmount))
                        .toFixed(2);

                    $('#subtotal-amount').text(subtotal.toFixed(2));
                    $('#tax-percent').text(taxPercent);
                    $('#tax-amount').text(taxAmount);
                    $('#shipping-amount').text(shipping.toFixed(2));
                    $('#discount-percent').text(discountPercent);
                    $('#discount-amount').text(discountAmount);
                    $('#receipt-total-amount').text(totalPayable);

                    $('#receiptModal').modal('show');
                }

                // Ensure other functions and event listeners are correctly defined here

                // Example event listener calling generateReceipt
                $('#print-receipt').on('click', generateReceipt);


                function loadProducts(categoryId = '') {
                    const userRole = "{{ auth()->user()->roles()->first()->name ?? 'guest' }}";
                    let url = categoryId ? `/${userRole}/pos/products-by-category/${categoryId}` :
                        `/${userRole}/pos/product`;

                    $.ajax({
                        url: url,
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(products) {
                            displayProducts(products);
                        },
                        error: function(xhr) {
                            console.error('Error fetching products:', xhr.responseText);
                        }
                    });
                }



                function displayProducts(products) {
                    const resultsDiv = $('#productResults');
                    resultsDiv.empty();
                    products.forEach(product => {
                        resultsDiv.append(`
                        <div class="col-6 col-sm-4 mb-3">
                            <div class="card product-item">
                                <img src="/${product.image}"  class="card-img-top" alt="${product.name}">
                                <div class="card-body text-center">
                                    <h6 class="card-title">${product.name}</h6>
                                    <p class="card-text">$${product.price}</p>
                                    <button class="btn btn-primary btn-sm add-to-cart" data-id="${product.id}" data-name="${product.name}" data-price="${product.price}">
                                        <i class="fas fa-cart-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `);
                    });
                    $('.add-to-cart').on('click', addToCart);
                }

                loadProducts();

                $('#category-select').on('change', function() {
                    loadProducts($(this).val());
                });
            });
        </script>
    @endsection
