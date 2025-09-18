<?php

namespace App\Http\Controllers\api\v1;

use App\Facade\CustomFacade;
use App\Http\Controllers\Controller;
use App\Http\Requests\api\v1\InstagramStoreRequest;
use App\Http\Traits\ImageTrait;
use App\Models\ChanelDataUpdateRequest;
use App\Models\Channel;
use App\Models\ChannelAudio;
use App\Models\ChannelContentCategory;
use App\Models\ChannelCountry;
use App\Models\Inquiry;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class InstagramController extends Controller
{
  use ImageTrait;
  // channal data store
  public function storeInstagram(InstagramStoreRequest $request)
  {
    try {
      $userInstagram = Channel::where('type', 1)->where('user_id', auth()->user()->id)->first();
      if (!isset($request->id) && $userInstagram) {
        throw new Exception("Opps! You can not add more then one instagram account.");
      }

      $instaAccountData = [];
      // Verify ownership
      if ($request->auth_type == 0) {
        $instagramData = $this->getInstagramData($request->token_code);
        if (!empty($instagramData['error'])) {
          $errorMessage = $instagramData['error'];
          $errorDetails = isset($instagramData['details']) ? json_encode($instagramData['details']) : '';

          throw new Exception("Instagram authentication failed: {$errorMessage} {$errorDetails}");
      }

        // if ($instagramData['user_id'] !== $request->account_user_id) {
        //   throw new Exception("Authentication details not match with account user id.");
        // }
        $instaAccountData['channel_id'] = $instagramData['user_id'];
        $instaAccountData['channel_name'] = $instagramData['name'] ?? '';
        $instaAccountData['username'] = $instagramData['username'] ?? '';
        $instaAccountData['account_type'] = $instagramData['account_type'] ?? '';
        $instaAccountData['followers_count'] = $instagramData['followers_count'] ?? 0;
        $instaAccountData['follows_count'] = $instagramData['follows_count'] ?? 0;
        $instaAccountData['media_count'] = $instagramData['media_count'] ?? 0;
        $instaAccountData['published_at'] = Carbon::now();
      }

      // Youtube channel id check unique
      if(!isset($request->id) && isset($instaAccountData['channel_id']) && $instaAccountData['channel_id'] != "") {
        $accountCheck = Channel::where('channel_id', $instaAccountData['channel_id'])->first();
        if($accountCheck){
          throw new Exception("Instagram account already exists.");
        }
      } else {
        $accountCheck = Channel::where('id', $request->id)->first();
      }

      // Add instagram data
      if ($request->auth_type == 1) {
        // $instaAccountData['channel_id'] = $request->account_user_id;
        $instaAccountData['channel_name'] = $request->name;
        $instaAccountData['username'] = $request->username;
        $instaAccountData['account_type'] = $request->account_type;
        $instaAccountData['followers_count'] = $request->followers_count;
        $instaAccountData['follows_count'] = $request->follows_count;
        $instaAccountData['media_count'] = $request->media_count;
      }

      $instaAccountData['type'] = 1;
      $instaAccountData['user_id'] = auth()->user()->id;
      $instaAccountData['category_id'] = $request->category_id;
      $instaAccountData['email'] = $request->email;
      $instaAccountData['upload_time'] = $request->upload_time;
      $instaAccountData['description'] = $request->description;

      if ($request->video_type == 1) {
        $instaAccountData['minimum_price'] = $request->minimum_price;
        $instaAccountData['minimum_short_price'] = $request->minimum_short_price;
      } else if ($request->video_type == 2) {
        $instaAccountData['minimum_price'] = $request->minimum_price;
        $instaAccountData['minimum_short_price'] = null;
      } else if ($request->video_type == 3) {
        $instaAccountData['minimum_price'] = null;
        $instaAccountData['minimum_short_price'] = $request->minimum_short_price;
      }

      $instaAccountData['video_type'] = $request->video_type;
      $instaAccountData['video_length'] = $request->video_length;

      $isVerifed = 1;
      if ($request->auth_type == 1 && !isset($request->id)) {
        $instaAccountData['is_verified'] = 0;
      }

      if ($request->auth_type == 1 && isset($request->id)) {
        $fieldsToUpdate = [
          'followers_count' => $request->followers_count,
          'follows_count' => $request->follows_count,
          'media_count' => $request->media_count,
          'channel_name' => $request->name,
          'username' => $request->username,
          'email' => $request->email,
          'category_id' => $request->category_id
        ];

        $needsUpdate = false;
        foreach ($fieldsToUpdate as $field => $value) {
          if ($accountCheck->$field != $value) {
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

          $checkAvailableReq->type = $accountCheck->type; // Ensure 'type' is set
          $checkAvailableReq->save();

          // Unset unnecessary keys from $instaAccountData
          $unsetFields = [
            'channel_name',
            'username',
            'account_type',
            'followers_count',
            'follows_count',
            'media_count',
            'email',
            'category_id'
          ];

          foreach ($unsetFields as $field) {
            unset($instaAccountData[$field]);
          }
        }
      } else {
        if(isset($request->id)){
          ChanelDataUpdateRequest::where('chanel_id', $request->id)->delete();
        }
      }

      $instaAccountData['is_last_update'] = Carbon::now()->format('Y-m-d');

      if (isset($request->thumbnail) && $request->file('thumbnail') != null) {
        if (isset($request->id) && $request->id != "") {
          $fetchChannel = Channel::find($request->id);
          if ($fetchChannel->thumbnail != '') {
            $this->imageDelete($fetchChannel->thumbnail);
          }
        }
        $instaAccountData['thumbnail'] = $this->imageUpload($request, 'thumbnail', 'storage/image/channel/thumbnail');
      }

      $instaStore = Channel::updateOrCreate(['id' => $request->id], $instaAccountData);
      if (!$instaStore) {
        throw new Exception("Something went wrong. Instagram account can't add.");
      }

      $instagramId = $instaStore->id;
      // Store country
      ChannelCountry::where('channel_id', $instagramId)->delete();
      foreach ($request->country as $country) {
        ChannelCountry::create(['channel_id' => $instagramId, 'country_id' => $country]);
      }

      //store audio
      ChannelAudio::where('channel_id', $instagramId)->delete();
      foreach ($request->audio_id as $audio) {
        ChannelAudio::create(['channel_id' => $instagramId, 'audio_id' => $audio]);
      }

      //store content category
      ChannelContentCategory::where('channel_id', $instagramId)->delete();
      foreach ($request->content_cat_id as $content) {
        ChannelContentCategory::create(['channel_id' => $instagramId, 'content_cat_id' => $content]);
      }

      // Fetch channel data
      $fetchAccount = Channel::with('audio', 'category', 'contentCategory.contentCategoryDetails', 'countries', 'chanelDataUpdateRequest')->find($instagramId);
      $message = isset($request->id) ? "Instagram account update successfully." : "Instagram account create successfully.";
      return CustomFacade::successResponse($message, $fetchAccount);
    } catch (Exception $e) {
      $message = $e->getMessage() ?? 'Something went wrong. Please try again.';
      return CustomFacade::errorResponse($message);
    }
  }

  public function getInstagramData($code)
  {
    $instagramAppId     = env('INSTAGRAM_APP_ID');
    $instagramAppSecret = env('INSTAGRAM_APP_SECRET');
    $redirectUri        = env('INSTAGRAM_REDIRECT_URI');

    try {
      // Step 1: Exchange code for short-lived token
      $shortTokenResponse = Http::asForm()->post('https://api.instagram.com/oauth/access_token', [
          'client_id'     => $instagramAppId,
          'client_secret' => $instagramAppSecret,
          'grant_type'    => 'authorization_code',
          'redirect_uri'  => $redirectUri,
          'code'          => $code,
      ]);

      if ($shortTokenResponse->failed()) {
          return ['error' => 'Failed to get short-lived access token', 'details' => $shortTokenResponse->json()];
      }

      $shortTokenData = $shortTokenResponse->json();
      if (empty($shortTokenData['access_token'])) {
          return ['error' => 'Access token not found', 'details' => $shortTokenData];
      }

      $shortLivedToken = $shortTokenData['access_token'];

      // Step 2: Exchange short-lived token for long-lived token
      $longTokenResponse = Http::get('https://graph.instagram.com/access_token', [
          'grant_type'    => 'ig_exchange_token',
          'client_secret' => $instagramAppSecret,
          'access_token'  => $shortLivedToken,
      ]);

      if ($longTokenResponse->failed()) {
          return ['error' => 'Failed to get long-lived access token', 'details' => $longTokenResponse->json()];
      }

      $longTokenData = $longTokenResponse->json();
      if (empty($longTokenData['access_token'])) {
          return ['error' => 'Long-lived token not found', 'details' => $longTokenData];
      }

      $longLivedToken = $longTokenData['access_token'];

      // Step 3: Fetch user data
      $userResponse = Http::get('https://graph.instagram.com/me', [
          'fields' => 'id,username,user_id,name,account_type,profile_picture_url,followers_count,follows_count,media_count',
          'access_token' => $longLivedToken,
      ]);

      if ($userResponse->failed()) {
          return ['error' => 'Failed to fetch Instagram user data', 'details' => $userResponse->json()];
      }

      $userData = $userResponse->json();
      $userData['access_token'] = $longLivedToken; // Optional: return token for later use

      return $userData;

    } catch (\Exception $e) {
        return ['error' => 'Exception occurred', 'message' => $e->getMessage()];
    }
  }


  // Get login use channel list
  public function accountDetails(Request $request)
  {
    try {
      $fetchAccount = Channel::with('audio', 'category', 'contentCategory.contentCategoryDetails', 'countries', 'chanelDataUpdateRequest')->where('user_id',auth()->user()->id)
      ->where('type', 1)
      ->find($request->account_id);
      if (!$fetchAccount) {
        throw new Exception("Account not exist.");
      }

      $countryArray = collect($fetchAccount->countries)->pluck('name')->toArray();
      $country = implode(', ', $countryArray);
      $fetchAccount->country = $country;
      if($fetchAccount->video_length != null){
        $videoLengthArray = explode(':',$fetchAccount->video_length);
        $fetchAccount->display_video_length = $videoLengthArray[0]." Min ".$videoLengthArray[1]." Sec";
      }else{
        $fetchAccount->display_video_length = "";
      }

      if (!$fetchAccount) {
        throw new Exception("Account not exist.");
      }
      return CustomFacade::successResponse("Fetch account successfully.", $fetchAccount);
    } catch (Exception $e) {
      $message = $e->getMessage() ?? 'Something went wrong. Please try again.';
      return CustomFacade::errorResponse($message);
    }
  }

  public function accountDelete(Request $request)
  {
    try {
      $fetchAccount = Channel::where('user_id',auth()->user()->id)
      ->where('type', 1)
      ->find($request->account_id);

      if (!$fetchAccount) {
        throw new Exception("Account not exist.");
      }

      $inquirylist = Inquiry::where('channel_id',$request->account_id)->get();
      foreach ($inquirylist as $key => $inquiry) {
        Notification::where('related_id',$inquiry->id)->delete();
        $inquiry->delete();
      }
      ChannelCountry::where('channel_id', $request->account_id)->delete();
      ChanelDataUpdateRequest::where('chanel_id', $request->channel_id)->delete();
      $fetchAccount->delete();
      return CustomFacade::successResponse("Instagram account delete successfully.");

    } catch (Exception $e) {
      $message = $e->getMessage() ?? 'Something went wrong. Please try again.';
      return CustomFacade::errorResponse($message);
    }
  }
}
