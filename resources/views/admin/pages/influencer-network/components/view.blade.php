<div class="modal-header">
  <h4 class="modal-title"></h4>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
  </button>
</div>
<div class="modal-body">
  <div class="card card-primary card-outline">
      <div class="card-body box-profile">
        <label>Chhanel Details : </label>
          <div class="row">
              <div class="col-md-6">
                  <ul class="list-group list-group-unbordered mb-3">
                      <li class="list-group-item">
                          <b>Thumbnail :</b> <span class="float-right"><img class="location-image"
                            src="{{ $details->thumbnail }}"></span>
                      </li>
                      <li class="list-group-item">
                          <b>Chhanel Id :</b> <span class="float-right">{{ $details->channel_id }}</span>
                      </li>
                      <li class="list-group-item">
                          <b>Category:</b> <span class="float-right">{{ $details->category->name }}</span>
                      </li>
                      <li class="list-group-item">
                          <b>Contact Email:</b> <span class="float-right">{{ $details->email }}</span>
                      </li>
                      <li class="list-group-item">
                          <b>Video Type:</b> <span class="float-right">{{ ($details->video_type == 1) ? 'Full and Short' : (($details->video_type == 2) ? 'Full' : 'Short') }}</span>
                      </li>
                  </ul>
              </div>
              <div class="col-md-6">
                  <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>Image :</b> <span class="float-right"><img class="location-image"
                          src="{{ ($details->image) ? $details->image : url('/public/default/default.jpg') }}"></span>
                    </li>
                    <li class="list-group-item">
                        <b>Chhanel Name :</b> <span class="float-right">{{ $details->channel_name }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Content Category:</b> <span class="float-right">
                          @php
                            $categoryNames = [];
                            foreach ($details->contentCategory as $contentCategory) {
                                $conentCat = $contentCategory->contentCategoryDetails;
                                if ($conentCat->isNotEmpty()) {
                                    $categoryNames = array_merge($categoryNames, $conentCat->pluck('name')->toArray());
                                }
                            }
                          @endphp
                          {{ !empty($categoryNames) ? implode(', ', $categoryNames) : 'N/A' }}
                        </span>
                    </li>
                    <li class="list-group-item">
                        <b>Upload Time :</b> <span class="float-right">{{ $details->upload_time }}</span>
                    </li>
                        {{--
                      <li class="list-group-item ">
                          <b>Status :</b> <span class="float-right status-badge"></span>
                      </li> --}}
                  </ul>
              </div>
          </div>
      </div>
  </div>
</div>
