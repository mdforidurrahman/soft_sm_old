@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Edit Dealer</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.dealers.update', $dealer->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="dealer_id">Dealer ID*</label>
                                    <input type="text" name="dealer_id" class="form-control" value="{{ $dealer->dealer_id }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="shop_name">Shop Name*</label>
                                    <input type="text" name="shop_name" class="form-control" value="{{ $dealer->shop_name }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="proprietor_name">Proprietor Name*</label>
                                    <input type="text" name="proprietor_name" class="form-control" value="{{ $dealer->proprietor_name }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone_number">Phone Number*</label>
                                    <input type="text" name="phone_number" class="form-control" value="{{ $dealer->phone_number }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <label for="address">Address*</label>
                            <textarea name="address" class="form-control" rows="3" required>{{ $dealer->address }}</textarea>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ $dealer->email }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <label for="remarks">Remarks</label>
                            <textarea name="remarks" class="form-control" rows="2">{{ $dealer->remarks }}</textarea>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="{{ route('admin.dealers.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection