<div class="container">
    <!-- Header Section -->
    <div class="row">
        <div class="col-6">
            <h3>From</h3>
            <p><strong>{{ $expense->store->name }}</strong></p>
            <p>{{ $expense->store->address }}</p>
        </div>
        <div class="col-6 text-end">
            <h3>Bill to</h3>
            <p><strong>{{ $expense->user->name }}</strong> ({{ $expense->user->role }})</p>
            <p>{{ $expense->user->email }}</p>
            <p>{{ $expense->user->phone }}</p>
            <p>{{ $expense->user->address }}</p>
        </div>
    </div>

    <!-- Invoice Details -->
    <div class="row mt-4">
        <div class="col-6">
            <p><strong>Invoice No:</strong> {{ $expense->reference_no }}</p>
            <p><strong>Invoice Date:</strong> {{ \Carbon\Carbon::parse($expense->expense_date)->format('F d, Y') }}</p>
            <p><strong>Expense Category:</strong> {{ $expense->expenseCategory->name }}</p>
        </div>
        <div class="col-6 text-end">
            <p><strong>Due
                    Date:</strong> {{ $expense->paid_date ? \Carbon\Carbon::parse($expense->paid_date)->format('F d, Y') : 'Not Specified' }}
            </p>
        </div>
    </div>

    <!-- Table Section -->
    <table class="table table-bordered mt-4">
        <thead class="table-light">
        <tr>
            <th>Description</th>
            <th>Amount</th>
            <th>Tax</th>
            <th>Total</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>{{ $expense->expenseCategory->name }}</td>
            <td>${{ number_format($expense->total_amount, 2) }}</td>
            <td>Included</td>
            <td>${{ number_format($expense->total_amount, 2) }}</td>
        </tr>
        </tbody>
    </table>

    <!-- Payment Details -->
    <div class="row mt-4">
        <div class="col-6">
            <h6>Payment Method:</h6>
            <p>{{ $expense->payment_method ?? 'Not Specified' }}</p>
        </div>
        <div class="col-6 text-end">
            <h6>Balance Due:</h6>
            <p><strong>${{ number_format($expense->total_amount, 2) }}</strong></p>
        </div>
    </div>

    <!-- Notes -->
    <div class="row mt-4">
        <div class="col-12">
            <h6>Notes:</h6>
            <p>{{ $expense->note ?? 'No additional notes provided.' }}</p>
        </div>
    </div>

    <!-- Download Button -->
    <div class="row mt-4">
        <div class="col-12 text-center">
            <button onclick="downloadInvoicePDF({{ $expense->id }})" class="btn btn-primary">
                <i class="fas fa-download"></i> Download PDF
            </button>
        </div>
    </div>
</div>


<script>
    function downloadInvoicePDF(id) {
        window.location.href = `/{{ rtrim($role, '.') }}/expense/${id}/download-invoice-pdf`;
    }
</script>
