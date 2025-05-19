<div class="dropdown">
    <button class="btn btn-secondary dropdown-toggle" type="button" id="invoiceDropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
        Invoice
    </button>
    <ul class="dropdown-menu" aria-labelledby="invoiceDropdownMenuButton">
        <li>
            <a class="dropdown-item" href="{{ route($role . 'expense.downloadInvoice', $id) }}">Download as PDF</a>
        </li>
        <li>
            <a class="dropdown-item" href="javascript:void(0);" onclick="showInvoice('{{ route($role . 'expense.showInvoice', $id) }}')">See Invoice</a>
        </li>
    </ul>
</div>
