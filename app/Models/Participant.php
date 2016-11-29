<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\ParticipantVote;
use Session;

class Participant extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'ip_address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function participantVotes()
    {
        return $this->hasMany(ParticipantVote::class);
    }

    public function showName()
    {
        if ($this->name == config('settings.no_name')) {
            if (Session::get('locale') == 'en') {
               return trans('polls.no_name');
            }

            if (Session::get('locale') == 'ja'){
                return trans('polls.no_name');
            }
        }

        return $this->name;
    }
}
