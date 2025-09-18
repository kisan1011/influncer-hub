<?php

namespace App\Http\Controllers\api\v1;

use Exception;
use App\Models\User;
use App\Models\Channel;
use App\Models\Inquiry;
use App\Facade\CustomFacade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class InfluencerSearchController extends Controller
{
  // channelDetails show
  public function channelDetails(Request $request)
  {
    try {

      $fetchChannel = Channel::select('id', 'type', 'user_id', 'channel_id', 'channel_name', 'username', 'category_id', 'upload_time', 'image', 'description', 'minimum_price', 'minimum_short_price', 'thumbnail', 'video_type', 'video_length', 'video_count', 'view_count', 'subscriber_count', 'followers_count', 'follows_count', 'media_count', 'published_at','custom_url')
        ->with('audio', 'category', 'chanelDataUpdateRequest', 'contentCategory.contentCategoryDetails', 'countries','user:id,name,profile,cover,bio,is_profile_visible', 'user.channels:id,user_id,channel_name,image,subscriber_count')
        ->where('id',$request->channel_id)->first();

      if (!$fetchChannel) {
        throw new Exception("Channel/Account not exist.");
      }

      $countryArray = collect($fetchChannel->countries)->pluck('name')->toArray();
      unset($fetchChannel->countries);
      $country = implode(', ', $countryArray);
      $fetchChannel->country = $country;

      $fetchChannel->is_inquiry_sent = 0;

      if($fetchChannel->video_length != "" ){
        $videoLengthArray = explode(':',$fetchChannel->video_length);
        $fetchChannel->display_video_length = $videoLengthArray[0]." Min ".$videoLengthArray[1]." Sec";
      }else{
        $fetchChannel->display_video_length = "";
      }
      $fetchChannel->upload_time = Channel::$upload[$fetchChannel->upload_time];


      return CustomFacade::successResponse("Channel details fetch successfully.", $fetchChannel);
    } catch (Exception $e) {
      $message = $e->getMessage() ?? 'Something went wrong. Please try again.';
      return CustomFacade::errorResponse($message);
    }
  }

  public function channelDetailsWithAuth(Request $request)
  {
    try {

      $fetchChannel = Channel::select('id', 'user_id', 'channel_name', 'category_id', 'upload_time', 'image', 'description', 'minimum_price', 'minimum_short_price', 'thumbnail', 'video_type', 'video_length', 'video_count', 'view_count', 'subscriber_count', 'published_at','custom_url')
        ->with('audio', 'category', 'contentCategory.contentCategoryDetails', 'countries','user:id,name,profile,cover,bio,is_profile_visible', 'user.channels:id,user_id,channel_name,image,subscriber_count')
        ->where('id',$request->channel_id)->first();

      if (!$fetchChannel) {
        throw new Exception("Channel not exist.");
      }

      $userID = auth()->user()->id;

      $countryArray = collect($fetchChannel->countries)->pluck('name')->toArray();
      unset($fetchChannel->countries);
      $country = implode(', ', $countryArray);
      $fetchChannel->country = $country;

      $checkInquiry = Inquiry::where('channel_id' , $fetchChannel->id)->where('sender_id',  $userID)->first();
      $fetchChannel->is_inquiry_sent = 0;
      if($checkInquiry){
        $fetchChannel->is_inquiry_sent = 1;
      }

      if($fetchChannel->video_length != "" ){
        $videoLengthArray = explode(':',$fetchChannel->video_length);
        $fetchChannel->display_video_length = $videoLengthArray[0]." Min ".$videoLengthArray[1]." Sec";
      }else{
        $fetchChannel->display_video_length = "";
      }
      $fetchChannel->upload_time = Channel::$upload[$fetchChannel->upload_time];


      return CustomFacade::successResponse("Channel/Account details fetch successfully.", $fetchChannel);
    } catch (Exception $e) {
      $message = $e->getMessage() ?? 'Something went wrong. Please try again.';
      return CustomFacade::errorResponse($message);
    }
  }

  // Influencer serach and list
  // Unused right now
  public function influencerList(Request $request)
  {
    try {
      $query = User::select('id', 'name', 'channel_count', 'profile')->where('channel_count', '!=', '0')->with('channels.countries', 'channels.category', 'channels.audio');

      // Search keyword
      $searchChar = $request->search;
      $query->when(!empty($request->search), function ($q) use ($searchChar) {
        $q->where(function ($qe) use ($searchChar) {
          $qe->where('name', 'like', '%' . $searchChar . '%')
            ->orWhere(function ($p) use ($searchChar) {
              $p->whereHas('channels.category', function ($p1) use ($searchChar) {
                $p1->where('name', 'like', '%' . $searchChar . '%');
              });
            });
        });
      });

      // Search country
      $countries = array_filter(array_unique(explode(',', $request->country)));
      $query->when(!empty($request->country), function ($q) use ($countries) {
        $q->whereHas('channels.countries', function ($p1) use ($countries) {
          $p1->whereIn('country_id', $countries);
        });
      });

      // Search Category
      $categorys = array_filter(array_unique(explode(',', $request->category)));
      $query->when(!empty($request->category), function ($q) use ($categorys) {
        $q->whereHas('channels.category', function ($p1) use ($categorys) {
          $p1->whereIn('category_id', $categorys);
        });
      });

      // Search language
      $language = array_filter(array_unique(explode(',', $request->language)));
      $query->when(!empty($request->language), function ($q) use ($language) {
        $q->whereHas('channels.audio', function ($p1) use ($language) {
          $p1->whereIn('audio_id', $language);
        });
      });
      // Search Price
      $price = $request->price;
      $query->when((!empty($request->price['min']) && !empty($request->price['max'])), function ($q) use ($price) {
        $q->whereHas('channels', function ($p1) use ($price) {
          $p1->whereBetween('minimum_price', [(int)$price['min'], (int)$price['max']]);
        });
      });

      // Search subscribers
      $subscribers = $request->subscribers;
      $query->when((!empty($request->subscribers['min']) && !empty($request->subscribers['max'])), function ($q) use ($subscribers) {
        $q->whereBetween('subscriber_count', [(int)$subscribers['min'], (int)$subscribers['max']]);
      });

      $responseData =  $query->orderBy('id', 'desc')->paginate(9);
      return CustomFacade::successResponse("Influncer fetch successfully.", $responseData);
    } catch (Exception $e) {
      $message = $e->getMessage() ?? 'Something went wrong. Please try again.';
      return CustomFacade::errorResponse($message);
    }
  }

  // Channles serach and list
  public function searchChannellist(Request $request)
  {
    // DB::enableQueryLog();
    try {
      $query = Channel::select('id', 'type', 'user_id', 'channel_id', 'channel_name', 'username', 'category_id', 'upload_time', 'image', 'description', 'minimum_price', 'minimum_short_price', 'thumbnail', 'video_type', 'video_length', 'video_count', 'view_count', 'subscriber_count', 'followers_count', 'follows_count', 'media_count', 'published_at','custom_url')
      ->with('user:id,name,profile,cover');

      // Search keyword
      $searchChar = $request->search;
      $query->when(!empty($request->search), function ($q) use ($searchChar) {
        $q->where(function ($q) use ($searchChar) {
          $q->where('channel_name', 'like', '%' . $searchChar . '%');
          $q->orWhere('username', 'like', '%' . $searchChar . '%');
          $q->orWhere('minimum_price', 'like', '%' . $searchChar . '%');
          $q->orWhere(function ($p) use ($searchChar) {
            $p->whereHas('category', function ($p1) use ($searchChar) {
              $p1->where('name', 'like', '%' . $searchChar . '%');
            });
          });
          $q->orWhere(function ($p) use ($searchChar) {
            $p->whereHas('user', function ($p1) use ($searchChar) {
              $p1->where('name', 'like', '%' . $searchChar . '%');
            });
          });
        });
      });

      // Search content category
      $contentCategorys = array_filter(array_unique(explode(',', $request->content_cat_id)));
      $query->when(!empty($request->content_cat_id), function ($q) use ($contentCategorys) {
        $q->whereHas('contentCategory', function ($subQuery) use ($contentCategorys) {
          $subQuery->whereIn('content_cat_id', $contentCategorys);
        });
      });

      // Search video type
      $videoType = array_filter(array_unique(explode(',', $request->video_type)));
      if(!empty($request->video_type)){
        array_push($videoType,"1");
        $videoType =  array_unique($videoType);
      }

      $query->when(!empty($request->video_type), function ($q) use ($videoType) {
        $q->whereIn('video_type', $videoType);
      });

      // Search Category
      $categorys = array_filter(array_unique(explode(',', $request->category)));
      $query->when(!empty($request->category), function ($q) use ($categorys) {
        $q->whereIn('category_id', $categorys);
      });

      // Search country
      $countries = array_filter(array_unique(explode(',', $request->country)));
      $query->when(!empty($request->country), function ($q) use ($countries) {
        $q->whereHas('countries', function ($p1) use ($countries) {
          $p1->whereIn('country_id', $countries);
        });
      });

      // Search Price
      $videoTypeDiff = array_values(array_diff(array(1,2,3), $videoType));
      $query->when((isset($request->price_min) && isset($request->price_max)), function ($q) use ($request, $videoType, $videoTypeDiff) {
        $q->when((empty($videoType) || empty($videoTypeDiff)), function($q1) use ($request) {
          $q1->where(function ($q2) use ($request) {
            $q2->whereNotNull('minimum_price')->whereBetween('minimum_price', [(int)$request->price_min, (int)$request->price_max]);
          // })->orWhere(function ($q2) use ($request) {
            $q2->orWhereBetween('minimum_short_price', [(int)$request->price_min, (int)$request->price_max]);
          });
        });
        $q->when((!empty($videoTypeDiff) && $videoTypeDiff[0] == 3), function($q1) use ($request) {
          $q1->where(function ($q2) use ($request) {
            $q2->whereNotNull('minimum_price')->whereBetween('minimum_price', [(int)$request->price_min, (int)$request->price_max]);
          });
        });
        $q->when((!empty($videoTypeDiff) && $videoTypeDiff[0] == 2), function($q1) use ($request) {
          $q1->where(function ($q2) use ($request) {
            $q2->whereNotNull('minimum_short_price')->whereBetween('minimum_short_price', [(int)$request->price_min, (int)$request->price_max]);
          });
        });
      });

      // Search Language
      $language = array_filter(array_unique(explode(',', $request->language)));
      $query->when(!empty($request->language), function ($q) use ($language) {
        $q->whereHas('audio', function ($subQuery) use ($language) {
          $subQuery->whereIn('audio_id', $language);
        });
      });

      // type
       $query->when((isset($request->type)), function ($q) use ($request) {
        $q->where('type', $request->type);
      });

      // Search subscribers
      $query->when((isset($request->subscribers_min) && isset($request->subscribers_max)), function ($q) use ($request) {
        $q->whereBetween('subscriber_count', [(int)$request->subscribers_min, (int)$request->subscribers_max]);
      });

      // Search followers
      $query->when((isset($request->followers_min) && isset($request->followers_max)), function ($q) use ($request) {
        $q->whereBetween('followers_count', [(int)$request->followers_min, (int)$request->followers_max]);
      });

      // $responseData['max_price'] = floor(Channel::select('minimum_price')->where('status',Channel::STATUS_ACTIVE)->max('minimum_price'));
      // $responseData['min_price'] = floor(Channel::select('minimum_price')->where('status',Channel::STATUS_ACTIVE)->min('minimum_price'));
      // $responseData['min_subscriber'] = Channel::select('subscriber_count')->where('status',Channel::STATUS_ACTIVE)->min('subscriber_count');
      // $responseData['max_subscriber'] = Channel::select('subscriber_count')->where('status',Channel::STATUS_ACTIVE)->max('subscriber_count');

      $responseData['channels'] = $query->orderBy('id', 'desc')->paginate(12);
      // dd(DB::getQueryLog());
      return CustomFacade::successResponse("Channel details fetch successfully.", $responseData);
    } catch (Exception $e) {
      $message = $e->getMessage() ?? 'Something went wrong. Please try again.';
      return CustomFacade::errorResponse($message);
    }
  }

  // Top influencer channel wise list
  public function topInfluencer(Request $request)
  {
    try {
      $validator = Validator::make($request->all(), [
        'type' => [
            'required',
            Rule::in(['youtube', 'instagram']),
          ],
      ]);

      if ($validator->fails()) {
          //pass validator errors as errors object for ajax response
          return CustomFacade::validatorError($validator);
      }

      $type = 0;
      if($request->type == 'instagram'){
        $type = 1;
      }

      $channels = Channel::select('id', 'type', 'user_id', 'channel_id', 'channel_name', 'username', 'category_id', 'upload_time', 'image', 'description', 'minimum_price', 'minimum_short_price', 'thumbnail', 'video_type', 'video_length', 'video_count', 'view_count', 'subscriber_count', 'followers_count', 'follows_count', 'media_count', 'published_at','custom_url')->where('type', $type)->with('user:id,name,profile')
      ->when(($type == 'youtube'), function($q) use ($type) {
        $q->orderBy('subscriber_count', 'DESC');
      })
      ->when(($type == 'instagram'), function($q) use ($type) {
        $q->orderBy('followers_count', 'DESC');
      })
      ->groupBy('user_id')->limit(4)->get();

      return CustomFacade::successResponse("Top influncer fetch successfully.", $channels);
    } catch (Exception $e) {
      $message = $e->getMessage() ?? 'Something went wrong. Please try again.';
      return CustomFacade::errorResponse($message);
    }
  }
}
