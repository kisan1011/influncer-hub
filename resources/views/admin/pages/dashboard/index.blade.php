@extends('admin.layouts.master')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"> Dashboard </h1>
                </div>
                <div class="col-sm-6">
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row card-content">

            </div>
        </div>
    </section>
@endsection
@push('page_scripts')
    <script src="{{ asset('public/admin/page/dashboard.js') }}"></script>
@endpush
