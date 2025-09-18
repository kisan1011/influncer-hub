<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Plan extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected function description(): Attribute
    {
      return new Attribute(
        set: fn ($value) => $value != "" ? html_entity_decode($value) : null,
        get: fn ($value) => $value != "" ? $value : ''
      );
    }
    // Permission
    protected function permission(): Attribute
    {
      return new Attribute(
        set: fn ($value) => $value != "" ? json_encode($value) : json_encode([]),
        get: fn ($value) => $value != "" ? json_decode($value) : []
      );
    }

}
