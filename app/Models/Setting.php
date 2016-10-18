<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Poll;

class Setting extends Model
{
    protected $fillable = [
        'poll_id',
        'key',
        'value',
    ];

    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }
}
