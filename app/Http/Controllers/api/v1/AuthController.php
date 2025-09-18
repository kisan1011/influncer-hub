<?php

namespace App\Http\Controllers\api\v1;

use App\Facade\CustomFacade;
use App\Http\Controllers\Controller;
use App\Http\Requests\api\v1\ChangePasswordRequest;
use App\Http\Requests\api\v1\EmailVerifyRequest;
use App\Http\Requests\api\v1\GoogleLoginRequest;
use App\Http\Requests\api\v1\LoginRequest;
use App\Http\Requests\api\v1\RegisterRequest;
use App\Http\Requests\api\v1\ResetPasswordRequest;
use App\Jobs\SendOtpJob;
use App\Mail\OtpMail;
use App\Models\Channel;
use App\Models\ChannelCountry;
use App\Models\Inquiry;
use App\Models\Notification;
use App\Models\Otp;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Laravel\Passport\Client as OClient;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Passport\Token;

class AuthController extends Controller
{
  // User register
  public function register(RegisterRequest $request)
  {
    DB::beginTransaction();
    try {
      // Send otp
      if(!isset($request->otp)){
        $this->otpSend($request->email);
        DB::commit();
        return CustomFacade::successResponse("Please check your mailbox for email verification code.");
      }

      // Otp verify
      $fetchOtp = Otp::where(['type' => Otp::TYPE_VERIFICATION, 'email' => $request->email])->first();
      if(!$fetchOtp || ($fetchOtp && $fetchOtp->updated_at->addMinutes(5) < now()) || $fetchOtp->otp != $request->otp){
        throw new Exception("Invalid OTP.");
      }

      $fetchOtp->delete();
      $record = $request->except('password', 'role');
      $record['password'] = Hash::make($request->password);
      $record['role_id'] = $request->role;
      $record['email_verified_at'] = now();
      $createUser = User::create($record);
      DB::commit();
      if (!$createUser) {
        throw new Exception("Something went wrong. Please try again.");
      }
      $user = User::where('id', $createUser->id)->first();
      $tokenBody = $this->getTokenAndRefreshToken($user->email, 'kiPhosplni-&5l5#e+rlbi-e879hop@u');
      return CustomFacade::loginAndSignupSuccess('You have successfully registered!',$tokenBody,$user->loginResponse());
    } catch (Exception $e) {
      DB::rollBack();
      $message = $e->getMessage() ?? 'Something went wrong. Please try again.';
      return CustomFacade::errorResponse($message);
    }
  }

  // Social login
  public function googleLogin(GoogleLoginRequest $request)
  {
    try {
      try {
        $googleUser = Socialite::driver('google')->userFromToken($request->token);
      } catch (\Exception $e) {
          return CustomFacade::errorResponse('Token is expired.');
      }
      $user = User::select('id','name','email','profile', 'channel_count', 'type', 'google_id', 'role_id','status')->where('email', $googleUser->getEmail() ?? "")->first();
      if(!$user){
        $data['email'] = $googleUser->getEmail() ?? "";
        $data['name'] =$googleUser->getName() ?? "";
        $data['google_id'] = $googleUser->getId() ?? "";
        $data['password'] = bcrypt('gpass');
        $data['role_id'] = $request->role;
        $data['type'] = 2;
        $data['email_verified_at'] = now();
        $user = User::create($data);
        $message = "You have successfully registered!";
      } else {
        if($user->status == User::STATUS_INACTIVE){
          throw new Exception("Account is deactivated. Please contact admin.");
        }
        if((int)$user->role_id !== (int)$request->role){
          throw new Exception("Email is already register with different provider.");
        } else {
          $message = "You have successfully login!";
        }
      }
      $user = User::where('id', $user->id)->first();
      $tokenBody = $this->getTokenAndRefreshToken($user->email, 'kiPhosplni-&5l5#e+rlbi-e879hop@u');
      return CustomFacade::loginAndSignupSuccess($message,$tokenBody,$user->loginResponse());
    } catch (Exception $e) {
      $message = $e->getMessage() ?? 'Something went wrong. Please try again.';
      return CustomFacade::errorResponse($message);
    }
  }

  // User login
  public function login(LoginRequest $request)
  {
    try {
      $credentials = [
        'email' => $request->email,
        'password' => $request->password,
        'role_id' => $request->role,
      ];
      if (!Auth::attempt($credentials)) {
        throw new Exception("You are trying the wrong credentials.");
      }
      $user = $request->user();
      if($user->role_id == User::ROLE_ADMIN){
        throw new Exception("You are trying the wrong credentials.");
      }
      //Concurrent login not be allowed
      $user->tokens()->delete();
      $user = User::where('id', $user->id)->first();
      $tokenBody = $this->getTokenAndRefreshToken($user->email, 'kiPhosplni-&5l5#e+rlbi-e879hop@u');
      return CustomFacade::loginAndSignupSuccess('You have successfully login!',$tokenBody,$user->loginResponse());
    } catch (Exception $e) {
      $message = $e->getMessage() ?? 'Something went wrong. Please try again.';
      return CustomFacade::errorResponse($message);
    }
  }

  // Send otp mail for email verification link
  public function otpSend($email, $type = "1")
  {
    $otp = random_int(1000, 9999);
    Otp::updateOrCreate(['type' => $type, 'email' => $email], ['type' => $type, 'email' => $email, 'otp' => $otp]);
    $details = [
      'type' => ($type == Otp::TYPE_VERIFICATION) ? '1' : '2',
      'email' => $email,
      'otp' => $otp,
    ];
    // dispatch(new SendOtpJob($details));
    try {
      $email = new OtpMail($details);
      Mail::to($details['email'])->send($email);
    } catch (\Throwable $th) {
      return false;
    }

    return true;
  }

  // Forgot password send otp and verify
  public function forgotPassword(EmailVerifyRequest $request)
  {
    try {
      // Check email is Social login or not
      // $fetchEmail = User::where('email',$request->email)->value('google_id');
      $fetchEmail = User::where('email',$request->email)->first();
      // dd($fetchEmail);
      // if($fetchEmail && $fetchEmail != null){
      //   throw new Exception("unauthorized.");
      // }
      // Send otp
      if(!isset($request->otp)){
        $this->otpSend($request->email,'2');
        return CustomFacade::successResponse("Please check your mailbox for email verification code.");
      }

      // Otp verify
      $fetchOtp = Otp::where(['type' => Otp::TYPE_FORGOT_PASSWORD, 'email' => $request->email])->first();
      if(!$fetchOtp || ($fetchOtp && $fetchOtp->updated_at->addMinutes(5) < now()) || $fetchOtp->otp != $request->otp){
        throw new Exception("Invalid OTP.");
      }
      $fetchOtp->delete();
      return CustomFacade::successResponse("Email verified successfully.",['email'=>$request->email]);
    } catch (Exception $e) {
      $message = $e->getMessage() ?? 'unauthorized.';
      return CustomFacade::errorResponse($message);
    }
  }

  // Reset password
  public function resetPassword(ResetPasswordRequest $request)
  {
    try {
      $fetchUser = User::where('email', $request->email)->first();
      // User is social login
      // if(!$fetchUser){
      //   throw new Exception("unauthorized.");
      // }
      $fetchUser->password = Hash::make($request->password);
      $fetchUser->save();
      return CustomFacade::successResponse("Reset password successfully.");
    } catch (Exception $e) {
      $message = $e->getMessage() ?? 'unauthorized.';
      return CustomFacade::errorResponse($message);
    }
  }

  // accesstoken and refresh token get
  public function getTokenAndRefreshToken($email, $password)
  {
    try {
      $oClient = OClient::where('password_client', 1)->first();
      if (empty($oClient)) {
        throw new Exception('Password_client not found');
      }
      $http = new Client;
      $response = $http->request('POST', url('/oauth/token'), [
        'form_params' => [
          'grant_type' => 'password',
          'client_id' => $oClient->id,
          'client_secret' => $oClient->secret,
          'username' => $email,
          'password' => $password,
          'scope' => '*',
        ],
      ]);
      return json_decode((string) $response->getBody(), true);
    } catch (Exception $e) {
      $message = $e->getMessage() ?? 'unauthorized.';
      return CustomFacade::errorResponse($message);
    }
  }

  // get accesstoken using refreshtoken
  public function accessToken(Request $request)
  {
    $oClient = OClient::where('password_client', 1)->first();
    $http = new Client;
    try {
      $response = $http->request('POST', url('/oauth/token'), [
        'form_params' => [
          'grant_type' => 'refresh_token',
          'client_id' => $oClient->id,
          'client_secret' => $oClient->secret,
          'refresh_token' => $request->refresh_token,
        ],
      ]);
      $result = json_decode($response->getBody(), true);
      return response()->json($result);
    } catch (Exception $e) {
      return CustomFacade::errorResponse('Unauthorized.');
    }
  }

  // User account logout
  public function logout()
  {
    try {
      $user = Auth::user();
      $token = $user->token();
      $token->revoke();
      return CustomFacade::successResponse('You have been successfully logged out!');
    } catch (Exception $e) {
      $message = $e->getMessage() ?? 'Something went wrong. Please try again.';
      return CustomFacade::errorResponse($message);
    }
  }

  // User account Delete
  public function deleteAccount()
  {
    try {
      // Revoke all access tokens associated with the user
      auth()->user()->tokens->each(function (Token $token) {
        $token->revoke();
      });

      // Delete notification
      Notification::where('receiver_id',auth()->user()->id)->orwhere('sender_id',auth()->user()->id)->delete();

      // Delete inquiry
      Inquiry::where('receiver_id',auth()->user()->id)->orwhere('sender_id',auth()->user()->id)->delete();

      // Delete channel
      if(auth()->user()->role_id == User::ROLE_INFLUENCER){
        $channel = Channel::select('id')->where('user_id',auth()->user()->id)->first();
        if(isset($channel->id)){
          ChannelCountry::where('channel_id',$channel->id)->delete();
          $channel->delete();
        }
      }

      // Delete user
      User::where('id',auth()->user()->id)->delete();

      return CustomFacade::successResponse('Account delete successfully.');
    } catch (Exception $e) {
      $message = $e->getMessage() ?? 'Something went wrong. Please try again.';
      return CustomFacade::errorResponse($message);
    }
  }

  // Change password
  public function changePassword(ChangePasswordRequest $request)
  {
    try {
      $user = auth()->user();
      if (Hash::check($request->oldpassword, $user->password)) {
        $hashPassword = Hash::make($request->newpassword);
        User::updateOrCreate(['id' => $user->id], ['password' => $hashPassword]);
        return CustomFacade::successResponse('Password change successfully.');
      } else {
        throw new Exception("Old password not match.");
      }
    } catch (Exception $e) {
      $message = $e->getMessage() ?? 'Something went wrong. Please try again.';
      return CustomFacade::errorResponse($message);
    }
  }
}
