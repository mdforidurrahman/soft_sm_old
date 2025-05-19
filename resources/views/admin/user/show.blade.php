@extends('layouts.admin')

@section('title', 'Show User')

@push('style')
<link rel="stylesheet" href="{{ asset('backend/plugins/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('backend/plugins/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
@endpush

@section('content')

<x-breadcumb title="Show User" /> <a href="{{ url('admin/user') }}"><i class="bx bx-arrow-back"></i></a>

<div class="row">
    <div class="col-xl-9 mx-auto">
        <div class="card">
            <div class="card-body">

                <form class="row g-3 needs-validation" novalidate="" action="" method="POST" enctype="multipart/form-data">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name" class="form-label">Name</label>
                            <input readonly type="text" name="name" id="name" class="form-control" value="{{ $user->name }}">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>

                        <input type="email" readonly name="email" id="email" class="form-control" value="{{ $user->email }}">

                    </div>
                    <div class="col-md-6">
                        <label for="address" class="form-label">Address</label>

                        <input readonly type="address" name="address" id="address" class="form-control" value="{{ $user->address }}">

                    </div>
                    <div class="col-md-6">
                        <label for="phone">Phone</label>
                        <input readonly type="text" name="phone" id="phone" class="form-control" value="{{ $user->phone }}">
                    </div>

                    <div class="col-md-6">
                        <label for="phone">Role</label>
                        <select name="roles" id="role" class="form-control select2">
                            @foreach ($roles as $role)
                                <option disabled value="{{ $role->name }}" {{ $role->name ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>

                    </div>

                    <div class="col-md-6">
                        <label for="phone">Password</label>
                        <input readonly type="password" name="password" id="phone" class="form-control">
                    </div>

                    <div class="col-md-6 mt-3">
                        <div class="form-group">
                            <label for="image">Image</label>
                            <img class="form-check-input" src="{{ !empty($user->user->photo) ? url($user->photo) : url('upload/no_image.jpg') }}" alt="Admin" style="width:100px; height: 100px;">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


{{-- <div class="row">
    <div class="col-xl-9 mx-auto">

        <div class="card">
            <h1 class="card-title mt-3 ms-3" style="font-size: 18px; font-weight:bold;">User Information</h1><hr>
            <div class="card-body d-flex flex-col gap-2">
                <div class="col-12 col-md-6">
                    <h2>Name: {{ $user->name }}</h2>
                </div>
                <div class="col-12 col-md-6">
                    <h2>Name: {{ $user->name }}</h2>
                </div>
                <div class="col-12 col-md-6">
                    <h2>Name: {{ $user->name }}</h2>
                </div>
                <div class="col-12 col-md-6">
                    <h2>Name: {{ $user->name }}</h2>
                </div>
            </div>
        </div>
    </div>
</div> --}}
@endsection


@push('script')
<script src="{{ asset('backend/plugins/select2/js/select2.min.js') }}"></script>
<script src="{{ asset('backend/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
</script>

@endpush
