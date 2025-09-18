@extends('admin.layouts.master')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Business</h1>
                </div>
                <div class="col-sm-6">
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-checkable" id="business-show">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Profile</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Provider</th>
                                            <th>Verified</th>
                                            <th class="status-col">Status</th>
                                            <th class="action-col">Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <button type="submit" class="btn btn-danger" id="multiple_delete_btn" data-title="business"
                                disabled>
                                Delete selected
                                <span style="display: none" id="multiple_delete_loader">
                                    <i class="fa fa-spinner fa-spin" aria-hidden="true"></i>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="modal fade" id="modal_div">
        <div class="modal-dialog">
            <div class="modal-content" id="form-content">

            </div>
        </div>
    </div>

    <div class="modal fade show" id="modal-xl" aria-modal="true" role="dialog" style="padding-right: 17px;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content show_content">

            </div>
        </div>
    </div>
@endsection
@push('page_scripts')
    <script src="{{ asset('public/admin/page/business.js') }}"></script>
@endpush
