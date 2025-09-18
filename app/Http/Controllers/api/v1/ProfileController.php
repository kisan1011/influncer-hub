<?php

namespace App\Http\Controllers\api\v1;

use App\Facade\CustomFacade;
use App\Http\Controllers\Controller;
use App\Http\Requests\api\v1\CoverImageRequest;
use App\Http\Requests\api\v1\ProfileStoreRequest;
use App\Http\Traits\ImageTrait;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
  use ImageTrait;
  // Fetch profile data
  public function index()
  {
    try {
      $fetchUser = User::find(auth()->user()->id);
      return CustomFacade::successResponse("User Data fetch successfully.", $fetchUser->loginResponse());
    } catch (Exception $e) {
      $message = $e->getMessage() ?? 'Something went wrong. Please try again.';
      return CustomFacade::errorResponse($message);
    }
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function coverImage(CoverImageRequest $request)
  {
    $fetchUser = User::find(auth()->user()->id);
    if ($request->file('cover') != null) {
      if ($fetchUser->cover != '') {
        $this->imageDelete($fetchUser->cover);
      }
      $fetchUser->cover = $this->imageUpload($request, 'cover', 'storage/image/user/cover');
    }
    $fetchUser->save();
    return CustomFacade::successResponse("cover image update successfully.", ['cover' => $fetchUser->cover]);
  }


  // User profile update
  public function store(ProfileStoreRequest $request)
  {
    try {
      $fetchUser = User::with('role')->find(auth()->user()->id);
      $fetchUser->name = $request->name;
      $fetchUser->contact = $request->contact;
      if($fetchUser->role_id == User::ROLE_BUSINESS){
        if($request->business_name == null || $request->business_name == ''){
          throw new Exception("Business name is required.");
        }
        $fetchUser->business_name = $request->business_name;
      }
      $fetchUser->bio = $request->bio;
      if ($request->file('profile') != null) {
        if ($fetchUser->profile != '') {
          $this->imageDelete($fetchUser->profile);
        }
        $fetchUser->profile = $this->imageUpload($request, 'profile', 'storage/image/user/profile');
      }

      $fetchUser->save();
      return CustomFacade::successResponse("Profile updated successfully.", $fetchUser->loginResponse());
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
  public function destroy($id)
  {
    //
  }
}
