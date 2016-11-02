<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Option;
use App\Models\Participant;

class ParticipantVote extends Model
{
    protected $fillable = [
        'participant_id',
        'option_id',
        'created_at',
        'updated_at',
    ];

    public function option()
    {
        return $this->belongsTo(Option::class);
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }
}
