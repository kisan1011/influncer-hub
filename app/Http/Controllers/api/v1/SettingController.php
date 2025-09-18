<?php

namespace App\Http\Controllers\api\v1;

use App\Facade\CustomFacade;
use App\Http\Controllers\Controller;
use App\Http\Requests\api\v1\SettingRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;

class SettingController extends Controller
{
  public function setting(SettingRequest $request)
  {
    try {
      $fetchSetting = User::select('id','email_notification','is_profile_visible')->find(auth()->user()->id);
      if(!$fetchSetting){
        return CustomFacade::errorResponse("Setting not found.");
      }
      $fetchSetting->email_notification = $request->email_notification;
      $fetchSetting->promotional_notification = $request->promotional_notification;
      if(auth()->user()->role_id == User::ROLE_INFLUENCER){
        $fetchSetting->is_profile_visible = $request->is_profile_visible;
      }
      $fetchSetting->save();
      $setting = collect($fetchSetting);
      return CustomFacade::successResponse("Setting change successfully.", (auth()->user()->role_id == User::ROLE_INFLUENCER)?$setting->only(['email_notification','is_profile_visible','promotional_notification']) : $setting->only(['email_notification', 'promotional_notification']));
    } catch (Exception $e) {
      $message = $e->getMessage() ?? 'Something went wrong. Please try again.';
      return CustomFacade::errorResponse($message);
    }
  }
}
