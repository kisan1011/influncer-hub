<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Role extends Model
{
  use HasFactory;
  protected $guarded = ['id'];

  protected $fillable = [
    'name',
    'slug',
  ];

  const ROLE_ADMIN = "Admin";
  const ROLE_INFLUENCER = "Influencer";
  const ROLE_BUSINESS = "Business";

  public function user()
  {
    return $this->belongsTo(User::class, 'id', 'role_id');
  }
}
