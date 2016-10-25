<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Poll;

class Link extends Model
{
    protected $fillable = [
        'poll_id',
        'token',
        'link_admin',
    ];

    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }
}
