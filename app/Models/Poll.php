<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Option;
use App\Models\Setting;
use App\Models\Link;
use App\Models\Activity;
use App\Models\Comment;

class Poll extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status',
        'multiple',
        'location',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function options()
    {
        return $this->hasMany(Option::class);
    }

    public function settings()
    {
        return $this->hasMany(Setting::class);
    }

    public function links()
    {
        return $this->hasMany(Link::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
