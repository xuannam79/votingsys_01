<?php

namespace App\RepositoriesApi\Contracts;

interface UserRepositoryInterface
{
    public function createUser($input = []);
}
