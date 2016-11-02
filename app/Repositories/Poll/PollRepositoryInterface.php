<?php

namespace App\Repositories\Poll;

interface PollRepositoryInterface
{
    /*
     * Create a poll
     */
    public function addInfo($input);
    public function addOption($input, $pollId);
    public function addSetting($input, $pollId);
    public function addLink($pollId, $input);

    /*
     * Edit a poll
     */
    public function editInfor($input, $id);
    public function editPollOption($input, $id);
    public function editPollSetting($input, $id);
}
