<?php

namespace App\RepositoriesApi;

use App\Models\Link;
use App\RepositoriesApi\Contracts\LinkRepositoryInterface;
use Exception;

class LinkRepositoryEloquent extends AbstractRepositoryEloquent implements LinkRepositoryInterface
{
    public function __construct(Link $model)
    {
        parent::__construct($model);
    }
}
