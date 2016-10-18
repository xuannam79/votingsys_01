<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\ParticipantVote;

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
}
