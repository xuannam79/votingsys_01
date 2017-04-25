<?php

namespace App\RepositoriesApi\Contracts;

interface UserRepositoryInterface
{
    public function createUser($input = []);
    public function updateUser($inputs = [], $avatar = null, $id);
    public function resetPassword($inputs = [], $currentUser);
}
