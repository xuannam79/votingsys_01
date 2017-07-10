<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class SocialAccount extends Model
{
    const FRAMGIA_DRIVER = 'framgia';
    const FRAMGIA_PROVIDER = 'FramgiaProvider';

    protected $fillable = [
        'user_id',
        'provider_user_id',
        'provider',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
