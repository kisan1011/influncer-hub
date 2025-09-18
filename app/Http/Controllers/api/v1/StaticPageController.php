<?php

namespace App\Http\Controllers\api\v1;

use Exception;
use App\Models\ContactUs;
use App\Models\Subscribe;
use App\Models\Staticpage;
use App\Facade\CustomFacade;
use App\Jobs\SubscribeEmailJob;
use App\Http\Controllers\Controller;
use App\Http\Requests\api\v1\ContactUsRequest;
use App\Http\Requests\api\v1\StaticPageRequest;
use App\Http\Requests\api\v1\SubscribeEmailRequest;
use App\Mail\SubscriptionConfirmation;
use Illuminate\Support\Facades\Mail;

class StaticPageController extends Controller
{
  // Fetch static page data
  public function staticPage(StaticPageRequest $request)
  {
    try {
      $staticPage = Staticpage::select('type', 'description')->where('role_id', $request->type)->where('type', $request->page)->first();
      if (!$staticPage) {
        throw new Exception("Something went wrong. Please try again.");
      }
      $message = ucfirst($request->page) . " page fetch successfully.";
      return CustomFacade::successResponse($message, $staticPage);
    } catch (Exception $e) {
      $message = $e->getMessage() ?? 'Something went wrong. Please try again.';
      return CustomFacade::errorResponse($message);
    }
  }

  // subscribe
  public function subscribe(SubscribeEmailRequest $request)
  {
    try {
      $subscribe = Subscribe::create(['email'=>$request->email]);
      if (!$subscribe) {
        throw new Exception("Something went wrong. Please try again.");
      }
      try {
        $email = new SubscriptionConfirmation($request->email);
        Mail::to($request->email)->send($email);
      } catch (\Throwable $th) {
        return false;
      }
      // dispatch(new SubscribeEmailJob($request->email));
      return CustomFacade::successResponse($request->email . " subscribe successfully.");
    } catch (Exception $e) {
      $message = $e->getMessage() ?? 'Something went wrong. Please try again.';
      return CustomFacade::errorResponse($message);
    }
  }

  public function contactUs(ContactUsRequest $request){
    try{
      ContactUs::create([
          'first_name' => $request->first_name,
          'last_name' => $request->last_name,
          'email' => $request->email,
          'phone' => $request->phone,
          'message' => $request->message,
      ]);
      return CustomFacade::successResponse("Message sent successfully.");
    }catch (Exception $e){
        $message = $e->getMessage() ?? 'Something went wrong. Please try again.';
        return CustomFacade::errorResponse($message);
    }
  }
}
