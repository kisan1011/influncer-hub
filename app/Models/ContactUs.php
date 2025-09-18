<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ContactUs extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'message',
    ];

    public function fullName(): Attribute
    {
        return new Attribute(
        get: fn ($value) => $this->first_name." ".$this->last_name,
        );
    }

}
