<?php

namespace App\Models;

use App\QueryFilter;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Poll;
use App\Models\Participant;
use App\Models\Comment;
use App\Models\SocialAccount;
use App\Models\Activity;
use App\Models\Vote;

class User extends Authenticatable
{
    use Notifiable;

    const IS_ADMIN = 1;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'chatwork_id',
        'gender',
        'avatar',
        'role',
        'is_active',
        'token_verification',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function polls()
    {
        return $this->hasMany(Poll::class);
    }

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function socialAccounts()
    {
        return $this->hasMany(SocialAccount::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    public function getAvatarPath()
    {
       return preg_match('#^(http)|(https).*$#', $this->avatar)
            ? $this->avatar
            : asset('/' . config('settings.avatar_path') . '/' . $this->avatar);
    }

    public function showGender()
    {

        $trans = trans('user.label.gender');
        $config = config('settings.gender_constant');
        $data = $trans['other'];

        if ($this->gender == $config['male']) {
            $data = $trans['male'];
        }

        if ($this->gender == $config['female']) {
            $data = $trans['female'];
        }

        if ($this->gender == $config['']) {
            $data = $trans[''];
        }

        return $data;
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public function isAdmin()
    {
        return $this->role == User::IS_ADMIN;
    }

    public static function boot()
    {
        parent::boot();

        static::updating(function ($user) {
            $user->is_active = true;
            $user->token_verification = '';
        });
    }
}
