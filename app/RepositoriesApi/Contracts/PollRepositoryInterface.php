<?php

namespace App\RepositoriesApi\Contracts;

interface PollRepositoryInterface
{
    public function storePoll($input = []);

    public function editPoll($poll, $input);
    public function editOption($poll, $input);
}
