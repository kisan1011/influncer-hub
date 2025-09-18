<div class="modal-header">
    <h4 class="modal-title"></h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <div class="card card-primary card-outline">
        <div class="card-body box-profile">
            <div class="row">
                <div class="col-md-12">
                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Name :</b> <span class="float-right">{{ $contentCategory->name }}</span>
                        </li>
                        <li class="list-group-item">
                            <b>Slug :</b> <span class="float-right">{{ $contentCategory->slug }}</span>
                        </li>
                        <li class="list-group-item">
                            <b>Logo :</b> <span class="float-right"><img class="location-image"
                                    src="{{ $contentCategory->logo }}"></span>
                        </li>
                        <li class="list-group-item ">
                            <b>Status :</b> <span class="float-right status-badge">{!! $contentCategory->getStatus() !!}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
