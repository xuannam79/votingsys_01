<?php

namespace App\RepositoriesApi\Contracts;

interface ActivityRepositoryInterface
{
    public function getActivityByPollId($pollId);
}
