<div class="modal fade" id="returnPurchaseModal" tabindex="-1" aria-labelledby="returnPurchaseModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="returnPurchaseModalLabel">Return Purchase</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="returnPurchaseForm">
                    @csrf
                    <input type="hidden" name="purchase_id" id="return_purchase_id">

                    <!-- Basic Return Information -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">Return Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="return_date" class="form-label">Return Date*</label>
                                    <input type="datetime-local" class="form-control" id="return_date"
                                        name="return_date" required>
                                </div>
                                <div class="col-md-8">
                                    <label for="return_note" class="form-label">Return Note</label>
                                    <textarea class="form-control" id="return_note" name="return_note" rows="2"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Products Section -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">Products to Return</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Product Name</th>
                                            <th>Unit Price</th>
                                            <th>Purchase Quantity</th>
                                            <th>Quantity Remaining</th>
                                            <th>Return Quantity</th>
                                            <th>Return Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody id="returnProductTableBody"></tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="5" class="text-end"><strong>Total Return Amount:</strong>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control" id="total_return_amount"
                                                    name="total_return_amount" readonly>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="submitReturnForm()">Submit Return</button>
            </div>
        </div>
    </div>
</div>
<script>
    function openReturnModal(purchaseId) {
        // Reset form
        $('#returnPurchaseForm')[0].reset();
        $('#return_purchase_id').val(purchaseId);
        $('#returnProductTableBody').empty();

        // Set default return date to current datetime
        $('#return_date').val(new Date().toISOString().slice(0, 16));

        // Fetch returnable items
        // purchaseId
        $.ajax({
            url: "{{ route($role . 'purchase.return.items', ':purchaseId') }}".replace(':purchaseId',
                purchaseId),
            method: 'GET',
            success: function(response) {
                populateReturnTable(response);
                $('#returnPurchaseModal').modal('show');
            },
            error: function(xhr) {
                AjaxNotifications.error('Error fetching purchase items');
            }
        });

    }

    // function populateReturnTable(items) {
    //     const tbody = $('#returnProductTableBody');
    //     items.forEach((item, index) => {
    //         const row = `
    //             <tr data-item-id="${item.purchase_item_id}">
    //                 <td>${item.product_name}</td>
    //                 <td>$${item.unit_price.toFixed(2)}</td>
    //                 <td>${item.purchase_quantity.toFixed(2)} Pc(s)</td>
    //                 <td>${item.remaining_quantity.toFixed(2)} Pc(s)</td>
    //                 <td>
    //                     <input type="number"
    //                            class="form-control return-quantity"
    //                            min="0"
    //                            max="${item.remaining_quantity}"
    //                            step="0.01"
    //                            value="0"
    //                            onchange="updateReturnSubtotal(this)">
    //                 </td>
    //                 <td>
    //                     <input type="number"
    //                            class="form-control return-subtotal"
    //                            value="0"
    //                            readonly>
    //                 </td>
    //             </tr>
    //         `;
    //         tbody.append(row);
    //     });
    // }

    function populateReturnTable(response) {
        const tbody = $('#returnProductTableBody');
        tbody.empty(); // Clear existing rows

        const items = response.returnable_items || [];

        items.forEach((item, index) => {
            const row = `
            <tr data-item-id="${item.id}">
                <td>${item.product_name}</td>
                <td>$${parseFloat(item.unit_cost).toFixed(2)}</td>
                <td>${parseFloat(item.quantity).toFixed(2)} Pc(s)</td>
                <td>${parseFloat(item.max_returnable_quantity).toFixed(2)} Pc(s)</td>
                <td>
                    <input type="number"
                           class="form-control return-quantity"
                           min="0"
                           max="${item.max_returnable_quantity}"
                           step="0.01"
                           value="0"
                           onchange="updateReturnSubtotal(this)">
                </td>
                <td>
                    <input type="number"
                           class="form-control return-subtotal"
                           value="0"
                           readonly>
                </td>
            </tr>
        `;
            tbody.append(row);
        });
    }

    function updateReturnSubtotal(input) {
        const row = $(input).closest('tr');
        const quantity = parseFloat(input.value) || 0;
        const unitPrice = parseFloat(row.find('td:eq(1)').text().replace('$', ''));
        const subtotal = quantity * unitPrice;

        row.find('.return-subtotal').val(subtotal.toFixed(2));
        updateTotalReturn();
    }

    function updateTotalReturn() {
        let total = 0;
        $('.return-subtotal').each(function() {
            total += parseFloat($(this).val()) || 0;
        });
        $('#total_return_amount').val(total.toFixed(2));
    }

    function submitReturnForm() {
        const items = [];
        $('#returnProductTableBody tr').each(function() {
            const returnQty = parseFloat($(this).find('.return-quantity').val()) || 0;
            if (returnQty > 0) {
                items.push({
                    purchase_item_id: $(this).data('item-id'),
                    quantity: returnQty
                });
            }
        });

        const formData = {
            purchase_id: $('#return_purchase_id').val(),
            return_date: $('#return_date').val(),
            return_note: $('#return_note').val(),
            items: items
        };

        $.ajax({
            url: "{{ route($role . 'purchase.return.store') }}",
            method: 'POST',
            data: JSON.stringify(formData),
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#returnPurchaseModal').modal('hide');
                loadTable(); // Refresh the main purchase table
                AjaxNotifications.success('Purchase return processed successfully');
            },
            error: function(xhr) {
                AjaxNotifications.error('Error processing return');
            }
        });
    }
</script>
