<?php

use App\Http\Controllers\api\v1\AuthController;
use App\Http\Controllers\api\v1\BlogController;
use App\Http\Controllers\api\v1\ChannelController;
use App\Http\Controllers\api\v1\ContactUsController;
use App\Http\Controllers\api\v1\DashboardController;
use App\Http\Controllers\api\v1\DefaultController;
use App\Http\Controllers\api\v1\InfluencerSearchController;
use App\Http\Controllers\api\v1\inquiryController;
use App\Http\Controllers\api\v1\InstagramController;
use App\Http\Controllers\api\v1\NotificationController;
use App\Http\Controllers\api\v1\ProfileController;
use App\Http\Controllers\api\v1\SettingController;
use App\Http\Controllers\api\v1\StaticPageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
  return $request->user();
});

Route::group(['prefix' => 'v1'], function () {
// Route::middleware(['headerSecurity'])->prefix('v1')->group(function () {

  // register
  Route::post('signup', [AuthController::class, 'register']);

  // Social login
  Route::post('google-login', [AuthController::class, 'googleLogin']);

  // Forgot password
  Route::post('forgot-password', [AuthController::class, 'forgotPassword']);

  // Reset password
  Route::post('reset-password', [AuthController::class, 'resetPassword']);

  // User login
  Route::post('login', [AuthController::class, 'login']);

  // Auth middleware
  Route::middleware(['auth:api','checkuserstatus'])->group(function () {

    // get access token using refresh token
    Route::post('access-token', [AuthController::class, 'accessToken']);

    // Change password
    Route::post('change-password', [AuthController::class, 'changePassword']);

    Route::post('get-channel-details-auth', [InfluencerSearchController::class, 'channelDetailsWithAuth']);

    // influencer check
    Route::middleware('influencer')->group(function () {

      //Dashboard
      Route::post('dashboard', [DashboardController::class, 'index']);

      // Profile cover image upload
      Route::post('cover-image', [ProfileController::class, 'coverImage']);

      // channel name suggestion
      Route::get('channel-suggestions-list', [ChannelController::class, 'channelSuggestionList']);

      // channel store
      Route::post('channal', [ChannelController::class, 'store']);

      // authorize user statistics channel count update
      Route::get('channal-statistics-update', [ChannelController::class, 'statisticsUpdate']);

      // auth user channel list
      Route::get('channel-list', [ChannelController::class, 'channelList']);

      // Channel details
      Route::get('channel-details', [ChannelController::class, 'channelDetails']);

      // delete channel
      Route::get('delete-channel', [ChannelController::class, 'channelDelete']);

      // store instagram account
      Route::post('instagram_account', [InstagramController::class, 'storeInstagram']);
      Route::get('account-details', [InstagramController::class, 'accountDetails']);
      Route::get('account-delete', [InstagramController::class, 'accountDelete']);

    });

    // business check
    Route::middleware('business')->group(function () {
      // Send inquiry
      Route::post('send-inquiry', [inquiryController::class, 'inquirySend']);
    });


    // Show inquiry list
    Route::post('inquiry', [inquiryController::class, 'inquiryList']);
    // Inquiry chat message send
    Route::post('inquiry-message-send', [inquiryController::class, 'sendMessage']);
    Route::post('inquiry-message-list', [inquiryController::class, 'messageList']);

    // Profile page
    Route::get('profile', [ProfileController::class, 'index']);
    Route::post('profile', [ProfileController::class, 'store']);

    // Setting
    Route::post('setting', [SettingController::class, 'setting']);


    //  User logout
    Route::get('logout', [AuthController::class, 'logout']);

    // Delete account
    Route::get('delete-account', [AuthController::class, 'deleteAccount']);
  });

  // list of category
  Route::get('category-list', [ChannelController::class, 'categoryList']);

  // list of content category
  Route::get('content-category-list', [ChannelController::class, 'contentCatList']);

  // list of audio
  Route::get('audio-list', [ChannelController::class, 'audioList']);
  // list of country
  Route::get('country-list', [ChannelController::class, 'countryList']);

  // Static page
  Route::get('static-page', [StaticPageController::class, 'staticPage']);

  // Subscribe email
  Route::post('subscribe', [StaticPageController::class, 'subscribe']);

  // Contact us
  Route::post('contact-us', [StaticPageController::class, 'contactUs']);

  // Blog API endpoints
  Route::get('blogs', [BlogController::class, 'index']); // Supports search with ?q=query
  Route::get('blog/{slug}', [BlogController::class, 'show']); // Includes meta data
  Route::get('blogs/latest', [BlogController::class, 'latest']);

  // Common Controller
  Route::controller(DefaultController::class)->group(function() {
    Route::get('common', 'index');
  });
});

