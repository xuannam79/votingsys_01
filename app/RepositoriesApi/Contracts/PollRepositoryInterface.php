<?php

namespace App\RepositoriesApi\Contracts;

interface PollRepositoryInterface
{
    public function storePoll($input = []);

    public function editPoll($poll, $input);

    public function editOption($poll, $input);

    public function getPollWithLinks($id);

    public function getSettingsPoll($poll);

    public function vote($poll, $input);
}
