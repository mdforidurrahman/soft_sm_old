@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-between align-items-center mb-4">
        <div class="col-md-6">
            <h1>Dealer Details</h1>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('admin.dealers.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Basic Information</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th>Dealer ID</th>
                            <td>{{ $dealer->dealer_id }}</td>
                        </tr>
                        <tr>
                            <th>Shop Name</th>
                            <td>{{ $dealer->shop_name }}</td>
                        </tr>
                        <tr>
                            <th>Proprietor Name</th>
                            <td>{{ $dealer->proprietor_name }}</td>
                        </tr>
                        <tr>
                            <th>Phone Number</th>
                            <td>{{ $dealer->phone_number }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $dealer->email ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5>Additional Information</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th>Address</th>
                            <td>{{ $dealer->address }}</td>
                        </tr>
                        <tr>
                            <th>Remarks</th>
                            <td>{{ $dealer->remarks ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{ $dealer->created_at->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <th>Updated At</th>
                            <td>{{ $dealer->updated_at->format('d M Y') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('admin.dealers.edit', $dealer->id) }}" class="btn btn-primary">Edit</a>
    </div>
</div>
@endsection