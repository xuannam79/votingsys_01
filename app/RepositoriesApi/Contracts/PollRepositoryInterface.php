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

    public function getPollsOfUser($userId);

    public function closeOrOpen($poll);

    public function resetVoted($poll);

    public function comment($poll, $input);

    public function resultsVoted($poll);
}
