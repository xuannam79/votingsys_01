<?php

namespace App\Repositories\Comment;

use App\Models\Comment;
use App\Models\Activity;
use App\Repositories\BaseRepository;
use App\Repositories\Comment\CommentRepositoryInterface;

class CommentRepository extends BaseRepository implements CommentRepositoryInterface
{
    public function __construct(Comment $comment)
    {
        $this->model = $comment;
    }
}
