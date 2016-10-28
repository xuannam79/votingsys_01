<?php

namespace App\Repositories\Poll;

interface PollRepositoryInterface
{
    public function editInfor($input, $id);
    public function editPollOption($input, $id);
    public function editPollSetting($input, $id);
}
