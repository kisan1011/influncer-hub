<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
  use HasApiTokens, HasFactory, Notifiable;

  const ROLE_ADMIN = 1;
  const ROLE_INFLUENCER = 2;
  const ROLE_BUSINESS = 3;

  const NOTIFICATION_OFF = 0;
  const NOTIFICATION_ON = 1;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'name',
    'business_name',
    'email',
    'contact',
    'password',
    'google_id',
    'profile',
    'cover',
    'bio',
    'role_id',
    'channel_count',
    'status',
    'email_verified_at',
    'email_notification',
    'is_profile_visible',
    'promotional_notification',
    'type',
    'oauth_email',
    'oauth_channels_ids',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'email_verified_at' => 'datetime',
  ];

  protected $appends = ['current_plan'];

  protected static function boot()
    {
      parent::boot();
      static::created(function ($model) {
          if($model->role_id == self::ROLE_INFLUENCER){
            $plan = Plan::first();
            Subscription::create([
              'user_id' => $model->id,
              'plan_id' => $plan->id,
              'status' => 'active',
              'start_at' => Carbon::now()->format('Y-m-d H:i:s'),
              'ends_at' => Carbon::now()->addMonths($plan->period)->format('Y-m-d H:i:s'),
              'is_recurring' => 0,
            ]);
          }
      });
    }

  // Profile image
  public function profile(): Attribute
  {
    return new Attribute(
      get: fn ($value) =>
      ($value != null && file_exists(public_path($value))) ? url('/public/' . $value) : url('/public/default/user_placeholder.png'),
    );
  }
  // Cover image

  public function cover(): Attribute
  {
    return new Attribute(
      get: fn ($value) => ($value != null && file_exists(public_path($value))) ? url('/public/' . $value) : url('/public/default/default_cover.png'),
    );
  }

   //Type
   const TYPE_EMAIL = '1';
   const TYPE_GOOGLE = '2';

  //Status
  const STATUS_ACTIVE = '1';
  const STATUS_INACTIVE = '0';
  public static $status = [
    self::STATUS_ACTIVE => 'Active',
    self::STATUS_INACTIVE => 'Inactive',
  ];

  // Get status html
  public function getStatus()
  {
    $html = '---';
    if ($this->status == self::STATUS_ACTIVE) {
      $html = '<span class="p-2 badge badge-success navbar-badge">' . Self::$status[self::STATUS_ACTIVE] . '</span>';
    } elseif ($this->status == self::STATUS_INACTIVE) {
      $html = '<span class="p-2 badge badge-danger navbar-badge">' . Self::$status[self::STATUS_INACTIVE] . '</span>';
    }
    return $html;
  }

  // Get verified html
  public function getVerified()
  {
    $html = '---';
    if ($this->email_verified_at != '' || $this->email_verified_at != null) {
      $html = '<span class="badge badge-success navbar-badge"> Verified </span>';
    } else {
      $html = '<span class="badge badge-danger navbar-badge"> Not verified </span>';
    }
    return $html;
  }

  // Role
  public function role()
  {
    return $this->hasOne(Role::class, 'id', 'role_id')->select('id', 'name');
  }

  // User count
  public function scopeRoleUser($query)
  {
    return $query->where('role_id', '!=', self::ROLE_ADMIN);
  }

  // Channels
  public function channels()
  {
    return $this->hasMany(Channel::class,'user_id','id')->where('type', 0);
  }

  // Channels
  public function instaAccounts()
  {
    return $this->hasMany(Channel::class,'user_id','id')->where('type', 1);
  }

  // Channels
  public function channel()
  {
    return $this->hasOne(Channel::class,'user_id','id')->where('type', 0);
  }

  // emailVerified
  protected function emailVerified(): Attribute
  {
    return new Attribute(
      get: fn ($value) => ($this->attributes['email_verified_at'] == NULL) ? false : true,
    );
  }

  // promotionalNotification
  protected function promotionalNotification(): Attribute
  {
    return new Attribute(
      get: fn ($value) => ($this->attributes['promotional_notification'] == self::NOTIFICATION_OFF) ? false : true,
    );
  }

  // emailNotification
  protected function emailNotification(): Attribute
  {
    return new Attribute(
      get: fn ($value) => ($this->attributes['email_notification'] == self::NOTIFICATION_OFF) ? false : true,
    );
  }

   // promotionalNotification
   protected function isProfileVisible(): Attribute
   {
     return new Attribute(
       get: fn ($value) => ($this->attributes['is_profile_visible'] == self::NOTIFICATION_OFF) ? false : true,
     );
   }

  // loginResponse
  public function loginResponse()
  {
    $userData = [
      'id' => $this->attributes['id'],
      'name' => $this->attributes['name'],
      'email' => $this->attributes['email'],
      'email_verified' => $this->emailVerified,
      'profile' => $this->profile,
      'contact' => $this->attributes['contact'],
      'cover' => $this->cover,
      'promotional_notification' => $this->promotionalNotification,
      'email_notification' => $this->emailNotification,
      'role_id' => $this->role_id,
    ];
    if($this->attributes['role_id'] == self::ROLE_BUSINESS){
      $userData = array_merge($userData, [
        'business_name' => $this->attributes['business_name'],
      ]);
    } else if($this->attributes['role_id'] == self::ROLE_INFLUENCER){
      $channelCollection = collect($this->channels);
      $instaAccountCollection = collect($this->instaAccounts);
      $channelData = $channelCollection->map(function ($item,$key) {
        return [
          'id' => $item->id,
          'channel_name' => $item->channel_name,
          'image' => $item->image,
        ];
      });

      $instaAccountData = $instaAccountCollection->map(function ($item,$key) {
        return [
          'id' => $item->id,
          'account_name' => $item->channel_name,
          'image' => $item->image,
        ];
      });

      $userData = array_merge($userData, [
        'channel_count' => $this->attributes['channel_count'],
        'is_profile_visible' => $this->isProfileVisible,
        'bio' => $this->attributes['bio'],
        'channels' => $channelData,
        'insta_accounts' => $instaAccountData,
        'current_plan' => $this->currentPlan,
      ]);
    }
    return $userData;
  }
  public function findForPassport($username)
  {
      $customUsername = 'email';
      return $this->where($customUsername, $username)->first();
  }

  public function validateForPassportPasswordGrant($password)
    {
        $owerridedPassword = 'password';
        // Password is :   kiPhosplni-&5l5#e+rlbi-e879hop@u
        return Hash::check($password, '$2y$10$M94DXKQJiFyQmwfuM3YTR.STU7MnW1lt2g.t7Ic35RMmSRSmvuWx2');
    }

  public function subscriptions()
  {
      return $this->hasMany(Subscription::class);
  }
  protected function currentPlan(): Attribute
  {
      return new Attribute(
          get: function () {
              // Get the latest subscription
              $subscription = $this->subscriptions()
              ->with('plan')
              ->orderBy('ends_at', 'desc')
              ->latest()
              ->first();
              if ($subscription) {
                  $currentDate = Carbon::now();
                  $isActive = $subscription->status === 'active' &&  $currentDate->lessThanOrEqualTo($subscription->ends_at);
                  return [
                      'plan_id' => $subscription->plan_id,
                      'plan_name' => $subscription->plan->name ?? null, // Assuming the relationship is defined
                      'status' => $subscription->status,
                      'is_active' => $isActive,
                      'ends_at' => $subscription->ends_at,
                      'plan' => $subscription->plan,
                  ];
              }
              return null;
          }
      );
  }
}
