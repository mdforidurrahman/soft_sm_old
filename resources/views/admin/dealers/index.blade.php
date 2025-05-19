@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-between align-items-center mb-4">
        <div class="col-md-6">
            <h1>Dealers</h1>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('admin.dealers.create') }}" class="btn btn-primary">Add New Dealer</a>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            {{ $message }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th>Dealer ID</th>
                    <th>Shop Name</th>
                    <th>Proprietor</th>
                    <th>Phone</th>
                    <th width="280px">Action</th>
                </tr>
                @foreach ($dealers as $dealer)
                <tr>
                    <td>{{ $dealer->dealer_id }}</td>
                    <td>{{ $dealer->shop_name }}</td>
                    <td>{{ $dealer->proprietor_name }}</td>
                    <td>{{ $dealer->phone_number }}</td>
                    <td>
                        <form action="{{ route('admin.dealers.destroy', $dealer->id) }}" method="POST">
                            <a class="btn btn-info" href="{{ route('admin.dealers.show', $dealer->id) }}">Show</a>
                            <a class="btn btn-primary" href="{{ route('admin.dealers.edit', $dealer->id) }}">Edit</a>
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>

    <div class="mt-4">
        {!! $dealers->links() !!}
    </div>
</div>
@endsection