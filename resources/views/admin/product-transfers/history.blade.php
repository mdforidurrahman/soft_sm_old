@extends('layouts.admin')
@section('title', 'Product Transfer History')

@push('style')
    @include('import.css.datatable')
@endpush

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">Product Transfer History</div>
        <div class="card-body">
            <table class="table table-bordered table-striped" id="transferHistoryTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>From Store</th>
                        <th>To Store</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transfers as $transfer)
                        <tr>
                            <td>{{ $transfer->id }}</td>
                            <td>{{ $transfer->fromStore->name }}</td>
                            <td>{{ $transfer->toStore->name }}</td>
                            <td>{{ $transfer->storeProduct->name }}</td>
                            <td>{{ $transfer->quantity }}</td>
                            <td>
                                @switch($transfer->status)
                                    @case('pending')
                                        <span class="badge badge-warning">{{ ucfirst($transfer->status) }}</span>
                                        @break
                                    @case('accepted')
                                        <span class="badge badge-success">{{ ucfirst($transfer->status) }}</span>
                                        @break
                                    @case('rejected')
                                        <span class="badge badge-danger">{{ ucfirst($transfer->status) }}</span>
                                        @break
                                @endswitch
                            </td>
                            <td>{{ $transfer->created_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('script')
    @include('import.js.datatable')
    <script>
        $(function() {
            $('#transferHistoryTable').DataTable({
                responsive: true,
                order: [[0, 'desc']]
            });
        });
      
      
    </script>
@endpush
