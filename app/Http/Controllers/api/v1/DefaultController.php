<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use App\Models\Channel;
use App\Facade\CustomFacade;

class DefaultController extends Controller
{
  public function index() {
    $responseData['max_price'] = floor(Channel::select('minimum_price')->where('status', Channel::STATUS_ACTIVE)->max('minimum_price'));
    $responseData['min_price'] = floor(Channel::select('minimum_price')->where('status',Channel::STATUS_ACTIVE)->min('minimum_price'));
    $responseData['max_short_price'] = floor(Channel::select('minimum_short_price')->where('status', Channel::STATUS_ACTIVE)->max('minimum_short_price'));
    $responseData['min_short_price'] = floor(Channel::select('minimum_short_price')->where('status',Channel::STATUS_ACTIVE)->min('minimum_short_price'));
    $responseData['min_subscriber'] = Channel::select('subscriber_count')->where('status',Channel::STATUS_ACTIVE)->min('subscriber_count');
    $responseData['max_subscriber'] = Channel::select('subscriber_count')->where('status',Channel::STATUS_ACTIVE)->max('subscriber_count');
    $responseData['min_followers'] = Channel::select('followers_count')->where('status',Channel::STATUS_ACTIVE)->min('followers_count');
    $responseData['max_followers'] = Channel::select('followers_count')->where('status',Channel::STATUS_ACTIVE)->max('followers_count');
    return CustomFacade::successResponse('General information fetch successfully', $responseData);
  }
}
