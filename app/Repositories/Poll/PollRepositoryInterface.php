<?php

namespace App\Repositories\Poll;

interface PollRepositoryInterface
{
    /*
     * Create a poll
     */
    public function sendEmail($email, $view, $viewData, $subject);

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
    public function getDataTableResult($poll);
    public function showSetting($settings);

    /*
     * send mail again
     */
    public function sendMailAgain($poll, $link, $password);

    public function getNameOptionToDrawChart($poll, $isHasImage);

    public function getDataToDrawPieChart($poll, $isHasImage);
}
