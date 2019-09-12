<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSecuritySetting extends Model
{
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'email_verified',
        'otp_verified',
        'phone_verified'
    ];

    protected $hidden = [
      'email_verification_code'
    ];
}
