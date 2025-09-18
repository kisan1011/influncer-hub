<div class="modal-header">
    <h4 class="modal-title"></h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-12 col-sm-12">
            <div class="card card-primary card-outline card-outline-tabs">
                <div class="card-header p-0 border-bottom-0">
                    <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="custom-tabs-four-home-tab" data-toggle="pill"
                                href="#custom-tabs-four-home" role="tab" aria-controls="custom-tabs-four-home"
                                aria-selected="true">User details</a>
                        </li>
                        {{-- <li class="nav-item">
                            <a class="nav-link" id="custom-tabs-four-profile-tab" data-toggle="pill"
                                href="#custom-tabs-four-profile" role="tab" aria-controls="custom-tabs-four-profile"
                                aria-selected="false">Profile</a>
                        </li> --}}
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-four-tabContent">
                        <div class="tab-pane fade show active" id="custom-tabs-four-home" role="tabpanel"
                            aria-labelledby="custom-tabs-four-home-tab">
                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>Profile </b> <span class="float-right"><img class="location-image"
                                            src="{{ $user->profile }}"></span>
                                </li>
                                <li class="list-group-item">
                                    <b>Name </b> <span class="float-right">{{ $user->name ?? '' }}</span>
                                </li>
                                <li class="list-group-item">
                                    <b>Business name </b> <span
                                        class="float-right">{{ $user->business_name ?? '' }}</span>
                                </li>
                                <li class="list-group-item">
                                    <b>Email </b> <span class="float-right">{{ $user->email ?? '' }}</span>
                                </li>
                                <li class="list-group-item">
                                    <b>Phone no </b> <span class="float-right">{{ $user->contact ?? '' }}</span>
                                </li>
                                <li class="list-group-item">
                                    <b>Status</b> <span class="float-right status-badge">{!! $user->getStatus() !!}</span>
                                </li>
                            </ul>
                        </div>
                        {{-- <div class="tab-pane fade" id="custom-tabs-four-profile" role="tabpanel"
                            aria-labelledby="custom-tabs-four-profile-tab">
                            Mauris tincidunt mi at erat gravida, eget tristique urna bibendum. Mauris pharetra purus ut
                            ligula tempor, et vulputate metus facilisis. Lorem ipsum dolor sit amet, consectetur
                            adipiscing elit. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere
                            cubilia Curae; Maecenas sollicitudin, nisi a luctus interdum, nisl ligula placerat mi, quis
                            posuere purus ligula eu lectus. Donec nunc tellus, elementum sit amet ultricies at, posuere
                            nec nunc. Nunc euismod pellentesque diam.
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
