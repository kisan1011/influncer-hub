<?php

namespace App\Http\Controllers;

use Google\Service\Oauth2;
use Google\Service\YouTube;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Http;

class TestController extends Controller
{
  protected $instagramAppId;
  protected $instagramAppSecret;
  protected $redirectUri;

  public function __construct()
  {
      $this->instagramAppId = env('INSTAGRAM_APP_ID');
      $this->instagramAppSecret = env('INSTAGRAM_APP_SECRET');
      $this->redirectUri = env('INSTAGRAM_REDIRECT_URI');
  }

  // Social login button page
  public function loginPage(Request $request)
  {
    $authorizationUrl = "https://www.instagram.com/oauth/authorize";
    $queryParams = http_build_query([
        'client_id' => $this->instagramAppId,
        'redirect_uri' => $this->redirectUri,
        'scope' => 'instagram_business_basic,instagram_business_manage_comments,instagram_business_manage_messages,instagram_business_content_publish',
        'response_type' => 'code',
    ]);

    $instaAuthUrl = $authorizationUrl . '?' . $queryParams;

    return view('loginpage', ['instaAuthUrl' => $instaAuthUrl]);
  }

  // Social login
  public function redirectToGoogle()
  {
    return Socialite::driver('google')->redirect();
  }

  // Youtube code genrate
  public function redirectToYoutube()
  {
    return Socialite::driver('google')
            ->setScopes([OAuth2::USERINFO_EMAIL, YouTube::YOUTUBE_READONLY])
            ->redirect();
  }

  // Social login token generate
  public function handleGoogleCallback(Request $request)
  {
    dd($request->all());
    $user = Socialite::driver('google')->user();
    return view('loginpage',['token'=>$user->token]);
  }

  public function handleInstagramCallback(Request $request)
  {
    dd($request->all());
  }

  public function handleInstagramAuth(Request $request)
  {
     // Get the 'code' from the callback
     $code = $request->input('code');
     if (!$code) {
        dd("error code not found");
        //  return redirect()->withErrors(['error' => 'Authorization failed']);
     }

     // Exchange code for a short-lived access token
     $response = Http::asForm()->post('https://api.instagram.com/oauth/access_token', [
         'client_id' => $this->instagramAppId,
         'client_secret' => $this->instagramAppSecret,
         'grant_type' => 'authorization_code',
         'redirect_uri' => $this->redirectUri,
         'code' => $code,
     ]);

     $responseBody = $response->json();

     // Check if we got the access token
     if (isset($responseBody['access_token'])) {
         $accessToken = $responseBody['access_token'];

         // Step 3: Exchange the short-lived access token for a long-lived token
         $longLivedTokenResponse = Http::get('https://graph.instagram.com/access_token', [
             'grant_type' => 'ig_exchange_token',
             'client_secret' => $this->instagramAppSecret,
             'access_token' => $accessToken,
         ]);

         $longLivedToken = $longLivedTokenResponse->json()['access_token'];

         // Store the token in session or database
         session(['instagram_access_token' => $longLivedToken]);
         return $this->getInstagramData();
         dd($longLivedToken);
        //  return redirect()->with('success', 'Logged in with Instagram');
     } else {
        if(session('instagram_access_token')){
          return $this->getInstagramData();
        }
        dd($response);
     }

    //  return redirect()->withErrors(['error' => 'Failed to get access token']);
  }

  public function getInstagramData()
  {
      $accessToken = session('instagram_access_token');

      $response = Http::get('https://graph.instagram.com/v20.0/me', [
          'fields' => 'id,username,user_id,name,account_type,profile_picture_url,followers_count,follows_count,media_count',
          'access_token' => $accessToken,
      ]);
      return $response->json();
  }


}
