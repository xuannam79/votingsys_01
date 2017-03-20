<?php

namespace App\RepositoriesApi;

use App\Models\Link;
use App\RepositoriesApi\Contracts\LinkRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Exception;

class LinkRepositoryEloquent extends AbstractRepositoryEloquent implements LinkRepositoryInterface
{
    public function __construct(Link $model)
    {
        parent::__construct($model);
    }

    public function updateLinkUserAndAdmin($arrayLinks = [], $data = [])
    {
        DB::beginTransaction();
        try {
            $this->update(
                ['token' => $data['newLinkUser']],
                $arrayLinks['oldLinkUser']->id
            );

            $this->update(
                ['token' => $data['newLinkAdmin']],
                $arrayLinks['oldLinkAdmin']->id
            );
            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();

            return false;
        }
    }
}
