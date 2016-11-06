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

    /*
     * get data
     */
    public function getDataPollSystem();
    public function getTimeFirstVote($poll);
    public function getTimeLastVote($poll);
    public function getTotalVotePoll($poll);
    public function getOptionLargestVote($poll);
    public function getOptionLeastVote($poll);
    public function getDataTableResult($poll, $isRequiredEmail);
    public function showSetting($settings);

    /*
     * send mail again
     */
    public function sendMailAgain($poll, $link, $password);
}
