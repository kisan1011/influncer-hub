<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staticpage extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $fillable = [
        'role_id',
        'type',
        'description'
    ];

    // Type
    const TYPE_TERMS = 'terms';
    const TYPE_PRIVACY = 'privacy';
    const TYPE_SAFETY = 'safety';
    const TYPE_REFUND = 'refund';
    const TYPE_DISCLAIMER = 'disclaimer';
    const TYPE_DMCA_POLICY = 'dmcapolicy';
    const TYPE_COOKIE_CONSENT = 'cookieconsent';
    const TYPE_ABOUT_US = 'aboutus';
    public static $type = [
        self::TYPE_TERMS => 'Terms',
        self::TYPE_PRIVACY => 'Pricacy',
        self::TYPE_SAFETY => 'Safety',
        self::TYPE_DISCLAIMER => 'Disclaimer',
        self::TYPE_DMCA_POLICY => 'DMCA Policy',
        self::TYPE_COOKIE_CONSENT => 'Cookie Consent',
        self::TYPE_ABOUT_US => 'About Us'
    ];
}
