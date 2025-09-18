@extends('admin.layouts.master')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Influencer</h1>
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
                                <table class="table table-bordered table-checkable" id="influencer-show">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>User Name</th>
                                            <th>Contact Email</th>
                                            <th>Thumbnail</th>
                                            <th>{{ ($type == 'instagram') ?  'Account Name' : 'Channel Name' }}</th>
                                             @if ($type == 'instagram')
                                              <th>Account User Name</th>
                                             @endif
                                             <th>Category</th>
                                             <th>Content Category</th>
                                             @if ($type == 'instagram')
                                             <th>Followers</th>
                                             <th>Follows</th>
                                             <th>Media</th>
                                             @else
                                             <th>Subscriber</th>
                                             <th>Views</th>
                                             <th>Video</th>
                                             @endif
                                            <th>Verified</th>
                                            <th class="status-col">Status</th>
                                            <th class="action-col">Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <button type="submit" class="btn btn-danger" id="multiple_delete_btn" data-title="influencer"
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
    <script>
      var type = "{{ $type }}";
    </script>
    <script src="{{ asset('public/admin/page/influencer-network.js') }}"></>
@endpush
