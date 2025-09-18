@extends('admin.layouts.master')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Static Page</h1>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <!-- general form elements disabled -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">{{ ucfirst(str($page)->replace('-', ' ')) }}</h3>
                        </div>
                        <form id="static-form" name="static-form" method="post">
                            @csrf
                            <div class="card-body">
                                <input type="hidden" name="type" value="{{ $pageData->type ?? $type }}">
                                <input type="hidden" name="role_id" value="{{ $pageData->role_id ?? $role_id }}">
                                <div class="form-group">
                                    <textarea class="form-control" name="description" rows="3" placeholder="Enter description"> {{ $pageData->description ?? '' }}</textarea>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary float-right" id="btn">Save <span
                                        style="display: none" id="loader"><i
                                            class="fa fa-spinner fa-spin"></i></span></button>
                            </div>
                        </form>
                        <!-- /.card-body -->
                    </div>
                </div>
            </div>
    </section>
@endsection
@push('page_scripts')
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.22.1/ckeditor.js"></script> --}}
    <script src="https://cdn.ckeditor.com/4.15.1/full-all/ckeditor.js"></script>
    <script src="{{ asset('public/admin/page/static.js') }}"></script>
@endpush
