@extends('admin.layouts.master')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"> Country </h1>
                </div>
                <div class="col-sm-6">
                    <div class="float-sm-right">
                        <button type="button" class="btn btn-block btn-primary add_form" data-title="country"
                            data-action="{{ url('/admin/country/create') }}">Add country</button>
                    </div>
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
                                <table class="table table-bordered table-checkable" id="country-show">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Name</th>
                                            <th>Code</th>
                                            <th class="action-col">Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <button type="submit" class="btn btn-danger" id="multiple_delete_btn" data-title="country"
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
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="data_form" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="card-body">
                            <input type="hidden" class="form-control" id="id" name="id" value="">
                            <div class="form-group">
                                <label>Name : <span class="required-text">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" value=""
                                    placeholder="Enter name">
                            </div>
                            <div class="form-group">
                                <label>Code : <span class="required-text">*</span></label>
                                <input type="text" class="form-control" id="code" name="code" value=""
                                    placeholder="Enter code" oninput="this.value = this.value.toUpperCase();">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="submit" class="btn btn-primary">Submit </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <div class="modal fade show" id="modal-xl" aria-modal="true" role="dialog" style="padding-right: 17px;">
        <div class="modal-dialog modal-xl">
            <div class="modal-dialog">
                <div class="modal-content show_content">

                </div>
            </div>
        </div>
    </div>
@endsection
@push('page_scripts')
    <script src="{{ asset('public/admin/page/country.js') }}"></script>
@endpush