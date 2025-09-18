<?php

use App\Http\Controllers\admin\BusinessController;
use App\Http\Controllers\admin\BlogController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\CKEditorController;
use App\Http\Controllers\admin\ContactUsController;
use App\Http\Controllers\admin\ContentCategoryController;
use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\admin\InfluencerController;
use App\Http\Controllers\admin\ProfileController;
use App\Http\Controllers\admin\StaticPageController;
use App\Http\Controllers\admin\SubscriberController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\CountryController;
use App\Http\Controllers\admin\InfluencerNetworksController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
  return redirect('admin/login');
});

// optimeze clear
Route::get('/cache-clear', function () {
  Artisan::call('config:cache');
  Artisan::call('cache:clear');
  Artisan::call('route:cache');
  Artisan::call('route:clear');
  Artisan::call('config:clear');
  Artisan::call('view:clear');
  return "Cache is cleared";
});

// Migration
Route::get('/migration', function () {
  Artisan::call('migrate', ['--force' => true]);
  return "Migration Successfully.";
});

// Seeder
Route::get('/seed', function () {
  Artisan::call('db:seed');
  return "Seeder Successfully.";
});

// Social login token generate
Route::get('auth/login', [TestController::class, 'loginPage']);
Route::get('auth/instagram', [TestController::class, 'handleInstagramAuth']);
Route::get('auth/instagram/callback', [TestController::class, 'handleInstagramCallback']);

Route::get('auth/google', [TestController::class, 'redirectToGoogle']);
Route::get('auth/youtube', [TestController::class, 'redirectToYoutube']);
Route::get('google/callback', [TestController::class, 'handleGoogleCallback']);


// Admin prefix for admin panel
Route::group(['prefix' => 'admin'], function () {

  //  Admin auth start
  Auth::routes(['register' => false]);
  //  Admin auth end

  // Middleware for admin
  Route::group(['middleware' => ['Admin']], function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Profile
    Route::resource('profile', ProfileController::class)->except(['update', 'create', 'show', 'edit']);

    // influencer
    Route::resource('influencer', InfluencerController::class)->except(['update', 'create', 'edit', 'store']);
    Route::post('influencer/status-update', [InfluencerController::class, 'statusUpdate']);
    Route::post('delete-influencer', [InfluencerController::class, 'multiple_delete']);

    // Business
    Route::resource('business', BusinessController::class)->except(['update', 'create', 'edit', 'store']);
    Route::post('business/status-update', [BusinessController::class, 'statusUpdate']);
    Route::post('delete-business', [BusinessController::class, 'multiple_delete']);

    // Category
    Route::resource('category', CategoryController::class)->except(['update','create']);

    Route::post('delete-category', [CategoryController::class, 'multiple_delete']);

    // Content category
    Route::resource('content-category', ContentCategoryController::class)->except(['update','create']);
    Route::post('content-category/status-update', [ContentCategoryController::class, 'statusUpdate']);
    Route::post('delete-content-category', [ContentCategoryController::class, 'multiple_delete']);

    // Country
    Route::resource('country', CountryController::class)->except(['update','create']);
    Route::post('delete-country', [CountryController::class, 'multiple_delete']);


    // CKEditor file upload (legacy)
    Route::post('ckeditor/upload', [CKEditorController::class, 'upload'])->name('ckeditor.upload');

    // TinyMCE file upload
    Route::post('tinymce/upload', [CKEditorController::class, 'tinymceUpload'])->name('tinymce.upload');

    // InfluencerNetworks
    Route::get('/influencer-network/{type}', [InfluencerNetworksController::class, 'index']);
    Route::get('/influencer-network/show/{id}', [InfluencerNetworksController::class, 'showDetails']);

  });
});

