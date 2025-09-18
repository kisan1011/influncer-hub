@extends('admin.layouts.master')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"> Channel Category </h1>
                </div>
                <div class="col-sm-6">
                    <div class="float-sm-right">
                        <button type="button" class="btn btn-block btn-primary add_form" data-title="category"
                            data-action="{{ url('/admin/category/create') }}">Add channel category</button>
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
                                <table class="table table-bordered table-checkable" id="category-show">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Image</th>
                                            <th>Name</th>
                                            <th>Type</th>
                                            <th class="status-col">Status</th>
                                            <th class="action-col">Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <button type="submit" class="btn btn-danger" id="multiple_delete_btn" data-title="category"
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
                                <label>logo : <span class="required-text">*</span></label>
                                <input type="file" class="form-control " name="logo"
                                    onchange="load_preview_image(this);" style="height:auto;" accept="image/png, image/gif, image/jpeg" />
                            </div>
                            <div class="form-group">
                                <div id="preview_div" style="display: none;">
                                    <img class="preview-postor" id="image_preview" src="">
                                </div>
                            </div>
                              <div class="form-group">
                                <label>Type: <span class="required-text">*</span></label><br>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="type" id="type_youtube" value="0" checked>
                                    <label class="form-check-label" for="type_youtube">YouTube</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="type" id="type_instagram" value="1">
                                    <label class="form-check-label" for="type_instagram">Instagram</label>
                                </div>
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
    <script src="{{ asset('public/admin/page/category.js') }}"></script>
@endpush
