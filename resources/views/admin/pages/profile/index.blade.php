@extends('admin.layouts.master')
@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">{{ ucfirst($title) }}</h1>
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
                        <h3 class="card-title">Edit profile</h3>
                    </div>
                    <form id="profile_frm" name="profile_frm" method="post">
                        @csrf
                        <div class="card-body">
                            <input type="hidden" name="id" id="id" value="{{ $user->id }}">
                            <div class="row">
                                <div class="form-group col-md-12" id="password_note">
                                    <div class="callout callout-info">
                                        <h5><i class="icon fas fa-info"></i> Note :</h5>
                                        <p>Leave <b>Password</b> and <b>Confirm Password</b> empty, if you are not going
                                            to change the password.</p>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Name <span class="red">*</span></label>
                                        <input type="text" class="form-control" placeholder="Please enter name"
                                            id="name" name="name" value="{{ $user->name }}">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Email <span class="red">*</span></label>
                                        <input type="text" class="form-control" name="email"
                                            placeholder="Please enter email" id="email" value="{{ $user->email }}">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Password</label>
                                        <input type="password" class="form-control" placeholder="Please enter password"
                                            id="password" name="password">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Confirm Password</label>
                                        <input type="password" class="form-control"
                                            placeholder="Please enter confirm password" id="confirm_password"
                                            name="confirm_password">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Image</label>
                                        <input type="file" name="profile" id="profile" class="form-control p-1"
                                            placeholder="Enter Select Image" onchange="load_preview_image(this);"
                                            accept="image/x-png,image/jpg,image/jpeg">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div id="preview_div">
                                        <img class="profile-user-img img-fluid img-circle admin_profile"
                                            id="image_preview" src="{{ $user->profile }}">
                                    </div>
                                </div>
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
    <script src="{{ asset('public/admin/page/profile.js') }}"></script>
@endpush
