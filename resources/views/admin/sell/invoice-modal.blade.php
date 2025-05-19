<style>
    :root {
        --primary-color: #4f46e5;
        --secondary-color: #10b981;
        --accent-color: #f59e0b;
        --dark-color: #1f2937;
        --light-color: #f9fafb;
        --border-color: #e5e7eb;
    }

    .modern-invoice {
        max-width: 800px;
        margin: 0 auto;
        padding: 2rem;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        color: var(--dark-color);
        background: white;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
    }

    .invoice-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid var(--border-color);
    }

    .company-logo-container {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }

    .company-logo {
        max-width: 150px;
        margin-bottom: 1rem;
    }

    .store-name {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--primary-color);
    }

    .invoice-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--primary-color);
        margin: 0;
    }

    .invoice-meta {
        text-align: right;
    }

    .invoice-number {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--dark-color);
        margin-bottom: 0.5rem;
        letter-spacing: 1px;
    }

    .invoice-date {
        color: #6b7280;
    }

    .company-info {
        margin-bottom: 1.5rem;
    }

    .company-name {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 0.5rem;
    }

    .address {
        color: #6b7280;
        line-height: 1.5;
    }

    .client-info {
        display: flex;
        gap: 2rem;
        margin-bottom: 2rem;
    }

    .bill-to, .ship-to {
        flex: 1;
        padding: 1.5rem;
        background: var(--light-color);
        border-radius: 8px;
    }

    .section-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--primary-color);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .section-title svg {
        width: 1.25rem;
        height: 1.25rem;
    }

    .client-name {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .items-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 2rem;
    }

    .items-table th {
        text-align: left;
        padding: 0.75rem 1rem;
        background: var(--primary-color);
        color: white;
        font-weight: 500;
    }

    .items-table td {
        padding: 1rem;
        border-bottom: 1px solid var(--border-color);
    }

    .items-table tr:last-child td {
        border-bottom: none;
    }

    .items-table tr:nth-child(even) {
        background-color: var(--light-color);
    }

    .product-name {
        font-weight: 500;
    }

    .product-sku {
        font-size: 0.875rem;
        color: #6b7280;
    }

    .text-right {
        text-align: right;
    }

    .totals-table {
        width: 100%;
        max-width: 300px;
        margin-left: auto;
        border-collapse: collapse;
    }

    .totals-table td {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid var(--border-color);
    }

    .totals-table tr:last-child td {
        border-bottom: none;
        font-weight: 600;
        font-size: 1.125rem;
        color: var(--primary-color);
    }

    .payment-info {
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--border-color);
    }

    .payment-status {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-weight: 500;
        font-size: 0.875rem;
    }

    .status-paid {
        background-color: #ecfdf5;
        color: #065f46;
    }

    .status-pending {
        background-color: #fef3c7;
        color: #92400e;
    }

    .status-partial {
        background-color: #e0f2fe;
        color: #0369a1;
    }

    .notes {
        margin-top: 2rem;
        padding: 1rem;
        background: var(--light-color);
        border-radius: 8px;
    }

    .footer {
        margin-top: 3rem;
        text-align: center;
        color: #6b7280;
        font-size: 0.875rem;
    }
</style>

<div class="modern-invoice">
    <!-- Header Section -->
    <div class="invoice-header">
        <div class="company-logo-container">
            <!-- Replace with your actual logo path -->
            <img src="{{ asset('assets/img/SM-Sunlight-group-logo.png') }}" alt="SM Sunlight Group" class="company-logo">
            <div class="store-name">{{ $sell->store->name }}</div>
        </div>
        <div class="invoice-meta">
            <h1 class="invoice-title">INVOICE</h1>
            <div class="invoice-number">#{{ substr(hash('sha256', $sell->id), 0, 12) }}</div>
            <div class="invoice-date">{{ date('F j, Y', strtotime($sell->sell_date)) }}</div>
        </div>
    </div>

    <!-- Client Information -->
    <div class="client-info">
        <div class="bill-to">
            <h3 class="section-title">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Bill To
            </h3>
            <div class="client-name">{{ $sell->contact->name }}</div>
            <div class="client-details">
                <div>Phone: {{ $sell->contact->phone }}</div>
                <div>Customer ID: {{ $sell->contact->contact_id }}</div>
                <div>Address: {{ $sell->contact->village }}, {{ $sell->contact->post_office }}</div>
                <div>{{ $sell->contact->thana }}, {{ $sell->contact->district }}</div>
            </div>
        </div>
        
        @if($sell->shippingDetail)
        <div class="ship-to">
            <h3 class="section-title">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
                Shipping Info
            </h3>
            <div class="shipping-details">
                <div><strong>Method:</strong> {{ ucfirst($sell->shippingDetail->shipping_method) }}</div>
                <div><strong>Tracking:</strong> {{ $sell->shippingDetail->tracking_number }}</div>
                <div><strong>Expected:</strong> {{ date('M j, Y', strtotime($sell->shippingDetail->expected_delivery_date)) }}</div>
            </div>
        </div>
        @endif
    </div>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th>Item</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Tax</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sell->items as $item)
            <tr>
                <td>
                    <div class="product-name">{{ $item->product->name }}</div>
                    <div class="product-sku">SKU: {{ $item->product->sku }}</div>
                </td>
                <td>{{ $item->quantity }}</td>
                <td>${{ number_format($item->unit_cost_before_tax, 2) }}</td>
                <td>${{ number_format($item->tax_amount, 2) }}</td>
                <td class="text-right">${{ number_format($item->net_cost, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totals -->
    <table class="totals-table">
        <tr>
            <td>Subtotal:</td>
            <td class="text-right">${{ number_format($sell->total_before_tax, 2) }}</td>
        </tr>
        @if($sell->tax_amount > 0)
        <tr>
            <td>Tax:</td>
            <td class="text-right">${{ number_format($sell->tax_amount, 2) }}</td>
        </tr>
        @endif
        @if($sell->shippingDetail && $sell->shippingDetail->shipping_cost > 0)
        <tr>
            <td>Shipping:</td>
            <td class="text-right">${{ number_format($sell->shippingDetail->shipping_cost, 2) }}</td>
        </tr>
        @endif
        @if($sell->discount_amount > 0)
        <tr>
            <td>Discount:</td>
            <td class="text-right">-${{ number_format($sell->discount_amount, 2) }}</td>
        </tr>
        @endif
        @if($sell->advance_balance > 0)
        <tr>
            <td>Advance Paid:</td>
            <td class="text-right">-${{ number_format($sell->advance_balance, 2) }}</td>
        </tr>
        @endif
        <tr>
            <td><strong>Total Due:</strong></td>
            <td class="text-right"><strong>${{ number_format($sell->payment_due, 2) }}</strong></td>
        </tr>
    </table>

    <!-- Notes -->
    @if($sell->note)
    <div class="notes">
        <h3 class="section-title">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
            </svg>
            Notes
        </h3>
        <p>{{ $sell->note }}</p>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        SM Sunlight Group!
    </div>
</div>