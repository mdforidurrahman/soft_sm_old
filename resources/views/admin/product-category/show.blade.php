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
                            <input readonly type="text" name="name" id="name" class="form-control" value="{{ $ProductCat->name }}">
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
