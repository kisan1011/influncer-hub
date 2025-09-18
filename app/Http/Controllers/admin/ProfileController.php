<?php

namespace App\Http\Controllers\admin;

use App\Facade\CustomFacade;
use App\Http\Controllers\Controller;
use App\Http\Requests\admin\ProfileRequest;
use App\Http\Traits\ImageTrait;
use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
  use ImageTrait;

  // profile page show
  public function index()
  {
    $title = 'profile';
    $user = User::find(auth()->user()->id);
    return view('admin.pages.profile.index', compact('title', 'user'));
  }

  // profile data store
  public function store(ProfileRequest $request)
  {
    $post_data = $request->validated();
    $valid_user = User::find($request->id);
    if (isset($post_data['profile']) && $post_data['profile'] != '') {
      $directory = 'storage/image/Admin/profile';
      $post_data['profile'] = $this->imageUpload($request, 'profile', $directory);
      if ($valid_user->profile != '' && $valid_user->profile != NULL) {
        $this->imageDelete($valid_user->profile);
      }
    } else {
      unset($post_data['profile'], $post_data['confirm_password']);
    }
    $post_data['password'] = (@$post_data['password'] != '') ? bcrypt($post_data['password']) : $valid_user->password;
    unset($post_data['confirm_password']);
    User::where('id', $request->id)->update($post_data);
    return CustomFacade::successResponse("Profile updated successfully.");
  }
}
