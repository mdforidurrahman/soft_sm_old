<style>
    .dropdown-item {
        padding: 0.5rem 1rem;
        cursor: pointer;
    }

    .dropdown-item i {
        margin-right: 0.5rem;
        width: 1rem;
    }

    .delete-form .dropdown-item {
        width: 100%;
        text-align: left;
        background: none;
        border: none;
        color: inherit;
    }

    .delete-form .dropdown-item:hover {
        background-color: #f8f9fa;
        color: #dc3545;
    }
</style>
<div class="btn-group">
    <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
        Actions <span class="caret"></span>
    </button>
    <ul class="dropdown-menu dropdown-menu-left" role="menu">
        {{-- View Option --}}
        @if (isset($viewRoute) && $viewRoute)
            {{-- <li>
                <a class="dropdown-item" href="{{ route($role . $viewRoute, $id) }}">
                    <i class="fas fa-eye"></i> View
                </a>
            </li> --}}

            <li>
                <a class="dropdown-item" href="#"
                   onclick="openViewModal('{{ route($role . $viewRoute, $id) }}'); return false;">
                    <i class="fas fa-eye"></i> View
                </a>
            </li>
        @endif



        {{-- View Modal Option --}}
        @if (isset($viewModal) && $viewModal)
            <li>
                <a class="dropdown-item" href="#"
                   onclick="openViewModal('{{ route($role . $viewModalRoute, $id) }}'); return false;">
                    <i class="fas fa-eye"></i> View
                </a>
            </li>
        @endif

        {{--   Ledger Modal    --}}

        @if (isset($ledgerModal) && $ledgerModal)
            <li>
                <a class="dropdown-item" href="#"
                   onclick="viewLedgerModal('{{ route($ledgerModalRoute, $id) }}'); return false;">
                    <i class="fas fa-eye"></i> Ledger
                </a>
            </li>
        @endif
        {{--   Customer Pay Modal    --}}

        @if (isset($payLedgerModal) && $payLedgerModal)
            <li>
                <a class="dropdown-item" href="#"
                   onclick="openPaymentModal('{{ $id}}'); return false;">
                    <i class="fas fa-eye"></i> Pay
                </a>
            </li>
        @endif

        {{-- Edit Option --}}
        @if (isset($editRoute) && $editRoute)
            <li>
                <a class="dropdown-item" href="{{ route($role . $editRoute, $id) }}">
                    <i class="fas fa-edit"></i> Edit
                </a>
            </li>
        @endif

        {{-- Edit Modal Option --}}
        @if (isset($editModal) && $editModal)
            <li>
                <a class="dropdown-item" href="#"
                   onclick="openEditModal('{{ route($role . $editModalRoute, $id) }}'); return false;">
                    <i class="fas fa-edit"></i> Edit
                </a>
            </li>
        @endif

        {{-- Delete Option --}}
        @if (isset($deleteRoute) && $deleteRoute)
            <li>
                <form action="{{ route($role . $deleteRoute, $id) }}" method="POST" class="delete-form">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="dropdown-item delete-btn" data-id="{{ $id }}"
                            data-model="{{ $model ?? '' }}">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </form>
            </li>
        @endif

        {{-- Download Option --}}
        @if (isset($showDownload) && $showDownload && $downloadRoute)
            <li>
                <a class="dropdown-item" href="{{ route($role . $downloadRoute, $id) }}">
                    <i class="fas fa-download"></i> Download
                </a>
            </li>
        @endif

        {{-- PDF Option --}}
        @if (isset($showPdf) && $showPdf && $pdfRoute)
            <li>
                <a class="dropdown-item" href="{{ route($role . $pdfRoute, $id) }}">
                    <i class="fas fa-file-pdf"></i> PDF
                </a>
            </li>
        @endif

        {{-- Mail Option --}}
        @if (isset($showMail) && $showMail && $mailRoute)
            <li>
                <a class="dropdown-item" href="{{ route($role . $mailRoute, $id) }}">
                    <i class="fas fa-envelope"></i> Mail
                </a>
            </li>
        @endif

        {{-- Return Item --}}

        @if (isset($returnRoute) && $returnRoute)
            <li>
                <a class="dropdown-item" href="{{ route($role . $returnRoute, $id) }}">
                    <i class="fas fa-undo"></i> Return
                </a>
            </li>
        @endif

        {{-- Return Modal Item --}}

        @if (isset($returnModal) && $returnModal)
            <li>
                <a class="dropdown-item" href="#"
                   onclick="openReturnModal('{{ $id }}'); return false;">
                    <i class="fas fa-undo"></i> Return
                </a>
            </li>
        @endif

    </ul>
</div>
