<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Poll;

class Link extends Model
{
    protected $fillable = [
        'poll_id',
        'token',
    ];

    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }
}
