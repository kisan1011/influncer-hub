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
                        @if ($user->channel_count > 0)
                          <li class="nav-item">
                            <a class="nav-link" id="custom-tabs-four-youtube-channel-tab" data-toggle="pill"
                                href="#custom-tabs-four-youtube-channel" role="tab" aria-controls="custom-tabs-four-youtube-channel"
                                aria-selected="false">Youtube channel</a>
                          </li>
                        @endif
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-four-tabContent">
                        <div class="tab-pane fade show active" id="custom-tabs-four-home" role="tabpanel"
                            aria-labelledby="custom-tabs-four-home-tab">
                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>Profile</b> <span class="float-right"><img class="location-image"
                                            src="{{ $user->profile }}"></span>
                                </li>
                                <li class="list-group-item">
                                    <b>Name </b> <span class="float-right">{{ $user->name ?? "---" }}</span>
                                </li>
                                <li class="list-group-item">
                                    <b>Email </b> <span class="float-right">{{ $user->email ?? "---" }}</span>
                                </li>
                                <li class="list-group-item">
                                    <b>Phone no </b> <span class="float-right">{{ $user->contact ?? "---" }}</span>
                                </li>
                                <li class="list-group-item">
                                    <b>Status</b> <span class="float-right status-badge">{!! $user->getStatus() !!}</span>
                                </li>
                            </ul>
                        </div>
                        @if ($user->channel_count > 0)
                          <div class="tab-pane fade" id="custom-tabs-four-youtube-channel" role="tabpanel"
                              aria-labelledby="custom-tabs-four-youtube-channel-tab">
                              <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>Profile</b> <span class="float-right"><img class="location-image"
                                            src="{{ $user->channels[0]->image }}"></span>
                                </li>
                                <li class="list-group-item">
                                    <b>Channel Name </b> <span class="float-right">{{ $user->channels[0]->channel_name ?? "---" }}</span>
                                </li>
                                <li class="list-group-item">
                                    <b>Channel id </b> <span class="float-right">{{ $user->channels[0]->channel_id ?? "---" }}</span>
                                </li>
                                <li class="list-group-item">
                                    <b>Email </b> <span class="float-right">{{ $user->channels[0]->email ?? "---" }}</span>
                                </li>
                                <li class="list-group-item">
                                    <b>Category </b> <span class="float-right">{{ $user->channels[0]->category->name ?? "---" }}</span>
                                </li>
                                <li class="list-group-item">
                                    <b>Upload time </b> <span class="float-right status-badge">{!! $user->channels[0]->getUploadTime() !!}</span>
                                </li>
                                <li class="list-group-item">
                                    <b>Description </b> <span class="float-right">{!! $user->channels[0]->description ?? "---" !!}</span>
                                </li>
                                <li class="list-group-item">
                                    <b>Minimum price for video </b> <span class="float-right">{{ '$'. $user->channels[0]->minimum_price ?? 0 }}</span>
                                </li>
                                <li class="list-group-item">
                                  <b>Minimum price for shorts (Reels) </b> <span class="float-right">{{ '$' . $user->channels[0]->minimum_short_price ?? 0 }}</span>
                              </li>
                                <li class="list-group-item">
                                    <b>Video length </b> <span class="float-right">{{ $user->channels[0]->video_length ?? "00:00" }}</span>
                                </li>
                                <li class="list-group-item">
                                    <b>Video count </b> <span class="float-right">{{ $user->channels[0]->video_count ?? 0 }}</span>
                                </li>
                                <li class="list-group-item">
                                    <b>View count </b> <span class="float-right">{{ $user->channels[0]->view_count ?? 0 }}</span>
                                </li>
                                <li class="list-group-item">
                                    <b>Subscriber count </b> <span class="float-right">{{ $user->channels[0]->subscriber_count ?? 0 }}</span>
                                </li>
                                @php
                                  $countryArray = $user->channels[0]->countries->pluck('name')->toArray();
                                  $countries = implode(', ', $countryArray);
                                @endphp
                                <li class="list-group-item">
                                    <b>Countries </b> <span class="float-right">{{ $countries ?? "---" }}</span>
                                </li>
                                <li class="list-group-item">
                                    <b>Audio </b>
                                    <span class="float-right">
                                        @php
                                            $audioArr = $user->channels[0]->audio->pluck('name')->toArray();
                                            $audio = implode(', ', $audioArr);
                                        @endphp
                                        {{ $audio ?? "---"}}
                                    </span>
                                </li>
                                <li class="list-group-item">
                                  <b>Published </b> <span class="float-right">{{ $user->channels[0]->published_at ?? "---" }}</span>
                              </li>
                            </ul>
                          </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
