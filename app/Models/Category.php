<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;

class Category extends Model
{
  use HasFactory;
  protected $guarded = ['id'];

  protected $fillable = [
    'type',
    'name',
    'slug',
    'logo',
    'status',
  ];


  const TYPE_YOUTUBE = 0;

  //Status
  const STATUS_ACTIVE = '1';
  const STATUS_INACTIVE = '0';
  public static $status = [
    self::STATUS_ACTIVE => 'Active',
    self::STATUS_INACTIVE => 'In active',
  ];

  // Get status html
  public function getStatus()
  {
    $html = '---';
    if ($this->status == self::STATUS_ACTIVE) {
      $html = '<span class="badge badge-success navbar-badge">' . Self::$status[self::STATUS_ACTIVE] . '</span>';
    } elseif ($this->status == self::STATUS_INACTIVE) {
      $html = '<span class="badge badge-danger navbar-badge">' . Self::$status[self::STATUS_INACTIVE] . '</span>';
    }
    return $html;
  }

  public function name(): Attribute
  {
    return new Attribute(
      set: fn ($value) => [
        'name' => $value,
        'slug' => Str::slug($value)
      ],
    );
  }

  // Logo default value set
  public function logo(): Attribute
  {
    return new Attribute(
      get: fn ($value) => ($value != null && file_exists(public_path($value))) ? url('/public/' . $value) : url('/public/default/default.jpg')
    );
  }

  // Channel relation
  public function channel()
  {
    return $this->belongsTo(Channel::class, 'id', 'category_id');
  }
}
