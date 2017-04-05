<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Vote;
use App\Models\Poll;
use App\Models\ParticipantVote;

class Option extends Model
{
    protected $fillable = [
        'poll_id',
        'name',
        'image',
    ];

    protected $hidden = ['image', 'votes', 'participantVotes'];

    protected $appends = ['url_image'];

    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function participantVotes()
    {
        return $this->hasMany(ParticipantVote::class);
    }

    public function countVotes()
    {
        $count = config('settings.default_value');
        $this->votes ? $count += $this->votes->count() : '';
        $this->participantVotes ? $count += $this->participantVotes->count() : '';

        return $count;
    }

    public function listVoter()
    {
        $voters = [];

        foreach ($this->users as $user) {
            $voters[] = [
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->getAvatarPath(),
                'id' => collect([])->push($this->id),
                'created_at' => $user->pivot->created_at,
            ];
        }

        foreach ($this->participants as $participant) {
            $voters[] = [
                'name' => $participant->name,
                'email' => $participant->email,
                'avatar' => asset(config('settings.image_default_path')),
                'id' => collect([])->push($this->id),
                'created_at' => $participant->pivot->created_at,
            ];
        }

        return $voters;
    }

    public function showImage()
    {
        if ($this->image) {
            return asset(config('settings.option.path_image'). $this->image);
        }

        return asset(config('settings.option.path_image_default'));
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'votes')->withTimestamps();
    }

    public function participants()
    {
        return $this->belongsToMany(Participant::class, 'participant_votes')->withTimestamps();
    }

    public function getUrlImageAttribute()
    {
        $image = $this->attributes['image'];

        if (!isset($image)) {
            return $this->attributes['url_image'] = $image;
        }

        if (strpos($image, 'default')) {
            return $this->attributes['url_image'] = asset(config('settings.option.path_image_default'));
        }

        return $this->attributes['url_image'] = asset(config('settings.option.path_image'). $image);
    }

    public function setNameMix()
    {
        $pattern = '/(0[1-9]|[1-9]|[1-2][0-9]|3[0-1])[\/-](0[1-9]|[1-9]|1[0-2])[\/-][0-9]{4}\s*(([01]?[0-9]|2[0-3]):[0-5][0-9]){0,1}/';

        if (validateDate($this->name)) {
            return $this->name = " ; $this->name";
        }

        if (preg_match_all($pattern, $this->name, $match)) {
            $string = preg_replace($pattern, '%s', $this->name);

            $dateOption = implode(' ; ', head($match));

            return $this->name = "$string ; $dateOption";
        }
    }
}
