<?php

namespace App\Repositories\Link;

use App\Models\Link;
use App\Repositories\BaseRepository;
use App\Repositories\Comment\CommentRepositoryInterface;

class LinkRepository extends BaseRepository implements LinkRepositoryInterface
{
    public function __construct(Link $link)
    {
        $this->model = $link;
    }

    public function getPollByToken($token)
    {
        return $this->model->where('token', $token)->with('poll')->first();
    }
}
