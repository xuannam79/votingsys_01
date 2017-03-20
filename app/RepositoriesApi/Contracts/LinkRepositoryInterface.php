<?php

namespace App\RepositoriesApi\Contracts;

interface LinkRepositoryInterface
{
    public function updateLinkUserAndAdmin($arrayLinks = [], $data = []);
}
