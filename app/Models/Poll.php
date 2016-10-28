<?php

namespace App\Models;

use App\QueryFilter;
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

    public function countComments()
    {
        return $this->comments->count() ? $this->comments->count() : config('settings.default_value');
    }

    public function countParticipants()
    {
        if (!$this->options) {
            return config('settings.default_value');
        }

        $listVotes = collect();
        foreach($this->options as $option) {
            $votes = $option->votes->pluck('user_id')->unique();

            if (!$votes->isEmpty()) {
                $listVotes->push($votes);
            }

            $participantVotes = $option->participantVotes->pluck('participant_id')->unique();

            if (!$participantVotes->isEmpty()) {
                $listVotes->push($participantVotes);
            }
        }

        return $listVotes->unique()->count();
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public function getUserLink()
    {
        if (!$this->links) {
            return false;
        }

        foreach ($this->links as $link) {
            if ($link->link_admin == config('settings.default_value')) {
                return url('link') . '/' . $link->token;
            }
        }
    }

    public function getAdminLink()
    {
        if (!$this->links) {
            return false;
        }

        foreach ($this->links as $link) {
            if ($link->link_admin != config('settings.default_value')) {
                return url('link') . '/' . $link->token;
            }
        }
    }
}
