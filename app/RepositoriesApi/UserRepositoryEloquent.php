<?php

namespace App\RepositoriesApi;

use App\Models\User;
use App\RepositoriesApi\Contracts\UserRepositoryInterface;


class UserRepositoryEloquent extends AbstractRepositoryEloquent implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }
}
