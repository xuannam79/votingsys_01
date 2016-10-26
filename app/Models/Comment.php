<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Poll;

class Comment extends Model
{
    protected $fillable = [
        'user_id',
        'poll_id',
        'name',
        'content',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }

    public function showDefaultAvatar()
    {
        return asset('/' . config('settings.avatar_path') . '/' . config('settings.avatar_default'));
    }
}
