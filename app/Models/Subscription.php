<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class, 'user_id')->whereIn('status', ['active'])->where('ends_at', '>=', Carbon::now()->format('Y-m-d H:i:s'));
    }
}
