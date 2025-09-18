<?php

namespace App\Http\Controllers\api\v1;

use App\Facade\CustomFacade;
use App\Http\Controllers\Controller;
use App\Http\Requests\api\v1\ChannelNameSuggestionRequest;
use App\Http\Requests\api\v1\ChannelIdCheckRequest;
use App\Http\Requests\api\v1\ChannelStoreRequest;
use App\Http\Traits\ChannelTrait;
use App\Http\Traits\ImageTrait;
use App\Models\Audio;
use App\Models\Category;
use App\Models\ChanelDataUpdateRequest;
use App\Models\Channel;
use App\Models\ChannelAudio;
use App\Models\ChannelContentCategory;
use App\Models\ChannelCountry;
use App\Models\ContentCategory;
use App\Models\Country;
use App\Models\Inquiry;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Google\Service\Oauth2;
use Google\Service\YouTube;
use Google_Client;
use Illuminate\Http\Request;

class ChannelController extends Controller
{
  use ChannelTrait, ImageTrait;

  // Show active category list
  public function categoryList(Request $request)
  {
    try {
      $type = $request->type ?? 0;
      $fetchCategory = Category::select('id', 'name', 'logo')->where('type', $type)->where('status', Category::STATUS_ACTIVE)->get();
      return CustomFacade::successResponse("Category fetch successfully.", $fetchCategory);
    } catch (Exception $e) {
      $message = $e->getMessage() ?? 'Something went wrong. Please try again.';
      return CustomFacade::errorResponse($message);
    }
  }

  // Show active content category list
  public function contentCatList(Request $request)
  {
    try {
      $type = $request->type ?? 0;
      $fetchCategory = ContentCategory::select('id', 'name', 'logo')->where('type', $type)->where('status', ContentCategory::STATUS_ACTIVE)->get();
      return CustomFacade::successResponse("Content category fetch successfully.", $fetchCategory);
    } catch (Exception $e) {
      $message = $e->getMessage() ?? 'Something went wrong. Please try again.';
      return CustomFacade::errorResponse($message);
    }
  }

  // Show audio list
  public function audioList()
  {
    try {
      $fetchAudio = Audio::select('id', 'name')->get();
      return CustomFacade::successResponse("Audio fetch successfully.", $fetchAudio);
    } catch (Exception $e) {
      $message = $e->getMessage() ?? 'Something went wrong. Please try again.';
      return CustomFacade::errorResponse($message);
    }
  }

  //  Show country list
  public function countryList()
  {
    try {
      $fetchCountry = Country::select('id', 'name', 'code')->get();
      return CustomFacade::successResponse("Country fetch successfully.", $fetchCountry);
    } catch (Exception $e) {
      $message = $e->getMessage() ?? 'Something went wrong. Please try again.';
      return CustomFacade::errorResponse($message);
    }
  }

  //  Channal suggestion list
  public function channelSuggestionList(ChannelNameSuggestionRequest $request)
  {

    $client = new Google_Client();
    $client->setScopes([
      // Oauth2::USERINFO_EMAIL,
      // Oauth2::USERINFO_PROFILE,
      YouTube::YOUTUBE_READONLY
    ]);
    $client->setAccessToken($request->accesstoken);
    try {
      // $service1 = new Oauth2($client);
      // $userInfo = $service1->userinfo->get();
      // $channelEmail = $userInfo->email;

      $service = new YouTube($client);
    } catch (Exception $e) {
      return CustomFacade::errorResponse("Access token invalid.");
    }

    try {
      $response = $service->channels->listChannels('snippet', ['mine' => true]);
    } catch (Exception $e) {
      return CustomFacade::errorResponse("Please give all permission to add the channel.");
    }
    if (count($response->items) == 0) {
      return CustomFacade::successResponse("Authorize users don't have any youtube channel.");
    }

    foreach ($response->items as $channel) {
      $channelNameList[] = [
        'id' => $channel->id,
        'name' =>  $channel->snippet->title,
        'image' =>  $channel->snippet->thumbnails->default->url,
      ];
    }
    if (empty($channelNameList)) {
      return CustomFacade::errorResponse("Channel not avalible for this account.");
    }

    $channelIds = implode(',', collect($channelNameList)->pluck('id')->toArray());
    $updateData = [
      'oauth_channels_ids' => $channelIds,
      // 'oauth_email' => $channelEmail,
    ];
    User::where('id', auth()->user()->id)->update($updateData);
    return CustomFacade::successResponse("Fetch channel list successfully.", $channelNameList);
  }


  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    //
  }

  // channal data store
  public function store(ChannelStoreRequest $request)
  {

    try {
      $user = User::select('channel_count', 'oauth_email', 'oauth_channels_ids')->find(auth()->user()->id);
      if (!isset($request->id) && $user->channel_count > 0) {
        throw new Exception("Opps! You can not add more then one channel.");
      }
      // Youtube channel id check unique
      $channelCheck = Channel::where('type', 0)->where('channel_id', $request->youtube_channel_id)->first();
      if ($channelCheck && (!isset($request->id) || (isset($request->id) && $request->id != "" && $request->id != $channelCheck->id))) {
        throw new Exception("Youtube channel already exists.");
      }

      // Verify ownership
      if (!isset($request->id) && $request->auth_type == 0) {
        $channelIDArray = explode(',', $user->oauth_channels_ids);
        if (!in_array($request->youtube_channel_id, $channelIDArray)) {
          throw new Exception("Authentication details not match with channel id.");
        }
      }

      // Fetch youtube details
      if ($request->auth_type == 0) {
        $channelData = $this->getChannelDetails($request->youtube_channel_id);
        if ($channelData == false) {
          throw new Exception("Youtube Channel not found.");
        }
      } else {
        $channelData['channel_id'] = $request->youtube_channel_id;
        $channelData['channel_name'] = $request->channel_name;
        // $channelData['image'] = $request->image;
        $channelData['video_count'] = $request->video_count;
        $channelData['view_count'] = $request->view_count;
        $channelData['subscriber_count'] = $request->subscriber_count;

        if ($request->custom_url) {
          $channelData['custom_url'] = $request->custom_url;
        }

        if ($request->published_at) {
          $publishDate = Carbon::parse($request->published_at)->format('Y-m-d');
          $channelData['published_at'] = $publishDate;
        }
      }

      // Add channel data manual data
      $channelData['user_id'] = auth()->user()->id;
      $channelData['category_id'] = $request->category_id;
      // $channelData['content_cat_id'] = $request->content_cat_id;
      $channelData['email'] = $request->email;
      $channelData['upload_time'] = $request->upload_time;
      $channelData['description'] = $request->description;

      if ($request->video_type == 1) {
        $channelData['minimum_price'] = $request->minimum_price;
        $channelData['minimum_short_price'] = $request->minimum_short_price;
      } else if ($request->video_type == 2) {
        $channelData['minimum_price'] = $request->minimum_price;
        $channelData['minimum_short_price'] = null;
      } else if ($request->video_type == 3) {
        $channelData['minimum_price'] = null;
        $channelData['minimum_short_price'] = $request->minimum_short_price;
      }

      $channelData['video_type'] = $request->video_type;
      $channelData['video_length'] = $request->video_length;
      $channelData['is_last_update'] = Carbon::now()->format('Y-m-d');

      $isVerifed = 1;
      if ($request->auth_type == 1 && !isset($request->id)) {
        $channelData['is_verified'] = 0;
      }

      if ($request->auth_type == 1 && isset($request->id)) {
        $fieldsToUpdate = [
          'video_count' => $request->video_count,
          'view_count' => $request->view_count,
          'subscriber_count' => $request->subscriber_count,
          'channel_name' => $request->channel_name,
          'email' => $request->email,
          'category_id' => $request->category_id
        ];

        $needsUpdate = false;
        foreach ($fieldsToUpdate as $field => $value) {
          if ($channelCheck->$field != $value) {
            $needsUpdate = true;
            break;
          }
        }

        if ($needsUpdate) {
          $checkAvailableReq = ChanelDataUpdateRequest::firstOrNew(['chanel_id' => $request->id]);

          // Update only changed fields
          foreach ($fieldsToUpdate as $field => $value) {
            $checkAvailableReq->$field = $value;
          }

          $checkAvailableReq->type = $channelCheck->type; // Ensure 'type' is set
          $checkAvailableReq->save();

          // Unset unnecessary keys from $channelData
          $unsetFields = [
            'channel_name',
            'video_count',
            'view_count',
            'subscriber_count',
            'email',
            'category_id'
          ];

          foreach ($unsetFields as $field) {
            unset($channelData[$field]);
          }
        }
      } else {
        if (isset($request->id)) {
          ChanelDataUpdateRequest::where('chanel_id', $request->id)->delete();
        }
      }

      if (isset($request->thumbnail) && $request->file('thumbnail') != null) {
        if (isset($request->id) && $request->id != "") {
          $fetchChannel = Channel::find($request->id);
          if ($fetchChannel->thumbnail != '') {
            $this->imageDelete($fetchChannel->thumbnail);
          }
        }
        $channelData['thumbnail'] = $this->imageUpload($request, 'thumbnail', 'storage/image/channel/thumbnail');
      }
      // dd($channelData);
      $channelStore = Channel::updateOrCreate(['id' => $request->id], $channelData);
      if (!$channelStore) {
        throw new Exception("Something went wrong. Channel can't create.");
      }

      $channelId = $channelStore->id;
      // Store country
      ChannelCountry::where('channel_id', $channelId)->delete();
      foreach ($request->country as $country) {
        ChannelCountry::create(['channel_id' => $channelId, 'country_id' => $country]);
      }

      //store audio
      ChannelAudio::where('channel_id', $channelId)->delete();
      foreach ($request->audio_id as $audio) {
        ChannelAudio::create(['channel_id' => $channelId, 'audio_id' => $audio]);
      }

      //store content category
      ChannelContentCategory::where('channel_id', $channelId)->delete();
      foreach ($request->content_cat_id as $content) {
        ChannelContentCategory::create(['channel_id' => $channelId, 'content_cat_id' => $content]);
      }

      if (!isset($request->id) && $request->auth_type == 0) {
        $user = User::find(auth()->user()->id);
        $user->channel_count = $user->increment('channel_count');
        $user->oauth_email = "";
        $user->oauth_channels_ids = "";
        $user->save();
      }
      // Fetch channel data
      $fetchChannel = Channel::with('audio', 'category', 'contentCategory.contentCategoryDetails', 'countries', 'chanelDataUpdateRequest')->find($channelId);
      $message = isset($request->id) ? "Channel update successfully." : "Channel create successfully.";
      return CustomFacade::successResponse($message, $fetchChannel);
    } catch (Exception $e) {
      $message = $e->getMessage() ?? 'Something went wrong. Please try again.';
      return CustomFacade::errorResponse($message);
    }
  }

  // Channel statistics count update
  // Right now unused API
  public function statisticsUpdate()
  {
    try {
      $fetchChannelIds = Channel::where('user_id', auth()->user()->id)->pluck('channel_id');
      if ($fetchChannelIds->isNotEmpty()) {
        $this->channelStatisticsUpdate($fetchChannelIds);
      }
      return CustomFacade::successResponse("Channel statistics count update successfully.");
    } catch (Exception $e) {
      $message = $e->getMessage() ?? 'Something went wrong. Please try again.';
      return CustomFacade::errorResponse($message);
    }
  }

  // Get login use channel list
  public function channelList()
  {
    try {
      $fetchChannels = Channel::select('id', 'channel_name', 'image')->where('user_id', auth()->user()->id)->first();
      return CustomFacade::successResponse("Fetch channel list successfully.", $fetchChannels);
    } catch (Exception $e) {
      $message = $e->getMessage() ?? 'Something went wrong. Please try again.';
      return CustomFacade::errorResponse($message);
    }
  }

  // Get login use channel list
  public function channelDetails(Request $request)
  {
    try {
      $fetchChannel = Channel::with('audio', 'category', 'contentCategory.contentCategoryDetails', 'countries', 'chanelDataUpdateRequest')->where('type', 0)->where('user_id', auth()->user()->id)->find($request->channel_id);
      if (!$fetchChannel) {
        throw new Exception("Channel not exist.");
      }
      $countryArray = collect($fetchChannel->countries)->pluck('name')->toArray();
      $country = implode(', ', $countryArray);
      $fetchChannel->country = $country;
      if ($fetchChannel->video_length != null) {
        $videoLengthArray = explode(':', $fetchChannel->video_length);
        $fetchChannel->display_video_length = $videoLengthArray[0] . " Min " . $videoLengthArray[1] . " Sec";
      } else {
        $fetchChannel->display_video_length = "";
      }


      if (!$fetchChannel) {
        throw new Exception("Channel not exist.");
      }
      return CustomFacade::successResponse("Fetch channel successfully.", $fetchChannel);
    } catch (Exception $e) {
      $message = $e->getMessage() ?? 'Something went wrong. Please try again.';
      return CustomFacade::errorResponse($message);
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function channelDelete(ChannelIdCheckRequest $request)
  {
    $fetchChannel = Channel::find($request->channel_id);
    if ($fetchChannel && $fetchChannel->user_id != auth()->user()->id) {
      return CustomFacade::errorResponse("You can't able to delete the channel.");
    }
    $inquirylist = Inquiry::where('channel_id', $request->channel_id)->get();
    foreach ($inquirylist as $key => $inquiry) {
      Notification::where('related_id', $inquiry->id)->delete();
      $inquiry->delete();
    }
    ChannelCountry::where('channel_id', $request->channel_id)->delete();
    ChanelDataUpdateRequest::where('chanel_id', $request->channel_id)->delete();
    $fetchChannel->delete();
    $user = User::find(auth()->user()->id);
    $user->channel_count = $user->channel_count - 1;
    $user->save();
    return CustomFacade::successResponse("Channel delete successfully.");
  }
}
