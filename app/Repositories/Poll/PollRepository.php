<?php

namespace App\Repositories\Poll;

use App\Models\Activity;
use App\Models\Link;
use App\Models\Option;
use App\Models\Participant;
use App\Models\Poll;
use App\Models\Setting;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use File;
use Exception;
use DB;
use App\Repositories\BaseRepository;
use Mail;

class PollRepository extends BaseRepository implements PollRepositoryInterface
{
    public function __construct(Poll $poll)
    {
        $this->model = $poll;
    }

    public function find($id)
    {
        return $this->model->where('status', true)->with('user', 'settings', 'comments.user', 'options')->find($id);
    }

    public function getInitiatedPolls()
    {
        $currentUserId = auth()->user()->id;

        return $this->model->where('user_id', $currentUserId)->with('activities')->orderBy('id', 'DESC')->get();
    }

    public function getParticipatedPolls($voteRepository)
    {
        $listPollIds = $voteRepository->getListPollIdOfCurrentUser();
        $participantPolls = $this->model->whereIn('id', $listPollIds)->with('activities')->orderBy('id', 'DESC')->get();
        $participants = auth()->user()->participants;

        if ($participants) {
            foreach ($participants as $participant) {
                $participantVotes = $participant->participantVotes;
                if ($participantVotes) {
                    foreach ($participantVotes as $participantVote) {
                        $participantPolls->push($participantVote->option->poll);
                    }
                }
            }
        }

        return $participantPolls;
    }

    public function getClosedPolls()
    {
        $currentUserId = auth()->user()->id;

        return $this->model->where('user_id', $currentUserId)->where('status', false)->with('activities')->orderBy('id', 'DESC')->get();
    }

    public function findPollById($id)
    {
        return $this->model->find($id);
    }

    public function findClosedPoll($id)
    {
        return $this->model->where('status', false)->find($id);
    }

    public function getVoteIds($pollId)
    {
        $poll = $this->model->find($pollId);
        $options = $poll->options;
        $voteIds = [];

        if ($options) {
            foreach ($options as $option) {
                $votes = $option->votes;

                if ($votes) {
                    foreach ($votes as $vote) {
                        $voteIds[] = $vote->id;
                    }
                }
            }
        }

        return $voteIds;
    }

    public function getParticipantVoteIds($pollId)
    {
        $poll = $this->model->find($pollId);
        $options = $poll->options;
        $participantVoteIds = [];

        if ($options) {
            foreach ($options as $option) {
                $participantVotes = $option->participantVotes;

                if ($participantVotes) {
                    foreach ($participantVotes as $participantVote) {
                        $participantVoteIds[] = $participantVote->id;
                    }
                }
            }
        }

        return $participantVoteIds;
    }

    public function checkUserVoted($pollId, $voteRepository)
    {
        $voteIds = $this->getAllVoteIds($pollId);

        if ($voteIds) {
            $currentUserId = auth()->user()->id;
            $votes = $voteRepository->getVotesByVoteId($voteIds);
            foreach ($votes as $vote) {
                if ($vote->user_id == $currentUserId) {
                    return true;
                }
            }
        }

        $participants = auth()->user()->participants;

        if ($participants) {
            foreach ($participants as $participant) {
                $participantVotes = $participant->participantVotes;
                if ($participantVotes) {
                    foreach ($participantVotes as $participantVote) {
                        if ($participantVote->option->poll->id == $pollId) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    public function getAllVoteIds($pollId)
    {
        $currentUserId = auth()->user()->id;
        $poll = $this->model->find($pollId);
        $options = $poll->options;
        $voteIds = [];

        if ($options) {
            foreach ($options as $option) {
                $votes = $option->votes;

                if ($votes) {
                    foreach ($votes as $vote) {
                        if ($vote->user_id == $currentUserId) {
                            $voteIds[] = $vote->id;
                        }
                    }
                }
            }
        }

        return $voteIds;
    }

/*------------------------------------------------------------
 *                  [ADMIN] - POLL
 *------------------------------------------------------------*/

    /**
     *
     * Add information of Poll into database USER and POLL
     *
     * @param $input
     *
     * @return bool
     */
    public function addInfo($input)
    {
        $now = Carbon::now();

        try {
            $userId = User::insertGetId([
                'name' => $input['name'],
                'email' => $input['email'],
                'chatwork_id' => ($input['chatwork_id']) ? $input['chatwork_id'] : null,
                'created_at' => $now,
            ]);
            $pollId = Poll::insertGetId([
                'user_id' => $userId,
                'title' => $input['title'],
                'description' => ($input['description']) ? $input['description'] : null,
                'location' => ($input['location']) ? $input['location'] : null,
                'multiple' => $input['type'],
                'created_at' => $now,
            ]);

            return $pollId;
        } catch (Exception $ex) {
            return false;
        }
    }

    /**
     *
     * Add all option of a poll into database OPTION
     *
     * @param $input
     * @param $pollId
     *
     * @return bool
     */
    public function addOption($input, $pollId)
    {
        try {
            $options = $input['optionText'];
            $images = $input['optionImage'];
            $dataOptionInserted = [];
            $imageNames = $this->createFileName($images);
            $now = Carbon::now();

            foreach ($options as $key => $option) {
                $image = empty($images[$key]) ? null : $imageNames['optionImage'][$key];

                if ($option || $image) {
                    $dataOptionInserted[] = [
                        'poll_id' => $pollId,
                        'name' => ($option) ? $option : null,
                        'image' => $image,
                        'created_at' => $now,
                    ];
                }
            }

            if ($dataOptionInserted) {
                Option::insert($dataOptionInserted);
            }

            $this->updateImage($images, $imageNames);

            return true;
        } catch (Exception $ex) {
            return false;
        }

    }

    /**
     *
     * Create a array contain name of image randed by system
     *
     * @param $arrInputImage
     *
     * @return array
     */
    public function createFileName($arrInputImage)
    {
        $imageNames = [];

        foreach ($arrInputImage as $key => $image) {
            do {
                $imageNames['optionImage'][$key] = uniqid(rand(), true) . '.' . $image->getClientOriginalExtension();
                $path = public_path() . config('settings.option.path_image') . $imageNames['optionImage'][$key];
            } while (File::exists($path));
        }

        return $imageNames;
    }

    /**
     *
     * Delete old image and upload a new image
     *
     * @param $images
     * @param $imageNames
     * @param array $oldImages
     * @throws Exception
     */
    public function updateImage($images, $imageNames, $oldImages = [])
    {
        try {
            /*
             * delete old image
             */
            if (is_array($oldImages) && $oldImages) {
                foreach ($oldImages as $image) {
                    $path = public_path() . config('settings.option.path_image') . $image;
                    if (File::exists($path)) {
                        File::delete($path);
                    }
                }
            }

            /*
             * upload new image
             */
            $pathTo = public_path() . config('settings.option.path_image');

            foreach ($images as $key => $image) {
                $pathFrom = $pathTo . $imageNames['optionImage'][$key];
                $image->move($pathTo, $pathFrom);
            }
        } catch (Exception $ex) {
            throw new Exception(trans('polls.message.upload_image_fail'));
        }
    }

    /**
     *
     * Add all setting of a poll into database SETTING
     *
     * @param $input
     * @param $pollId
     *
     * @return bool
     */
    public function addSetting($input, $pollId)
    {
        try {
            $settings = $input['setting'];
            $value = $input['value'];
            $dataSettingInserted = [];
            $now = Carbon::now();

            foreach ($settings as $setting) {
                $dataSettingInserted[] = [
                    'poll_id' => $pollId,
                    'key' => $setting,
                    'value' => $this->getValueOfSetting($setting, $value),
                    'created_at' => $now,
                ];
            }

            if ($dataSettingInserted) {
                Setting::insert($dataSettingInserted);
            }

            return true;
        } catch (Exception $ex) {
            return false;
        }

    }

    /**
     *
     * Get value of settings have value
     *
     * @param $setting
     * @param $values
     *
     * @return null|string
     */
    public function getValueOfSetting($setting, $values)
    {
        $config = config('settings.setting');

        if ($setting == $config['custom_link']) {
            return $values['link'];
        }

        if ($setting == $config['set_limit']) {
            return $values['limit'];
        }

        if ($setting == $config['set_password']) {
            return bcrypt($values['password']);
        }

        return null;
    }

    /**
     *
     * Add link of poll into table LINK
     *
     * @param $pollId
     * @param $input
     * @return array|bool
     */
    public function addLink($pollId, $input)
    {
        try {
            $participantLink = str_random(config('settings.length_poll.link'));
            $administrationLink = str_random(config('settings.length_poll.link'));
            $linkConfig =  url("/") . config('settings.email.link_vote');

            if ($input['value']['link']) {
                $participantLink = $input['value']['link'];
            }
            /*
             * insert link of participant
             */
            Link::create([
                'poll_id' => $pollId,
                'token' => $participantLink,
                'link_admin' => config('settings.link_poll.vote'),
            ]);

            /*
             * insert link of administration
             */
            Link::create([
                'poll_id' => $pollId,
                'token' => $administrationLink,
                'link_admin' => config('settings.link_poll.admin'),
            ]);
            $linkReturn = [
                'participant' => $linkConfig . $participantLink,
                'administration' => $linkConfig . $administrationLink,
            ];

            return $linkReturn;
        } catch (Exception $ex) {
            return false;
        }
    }


    /**
     *
     * Send mail for participant and creator
     *
     * @param $email
     * @param $view
     * @param $viewData
     * @param $subject
     *
     * @throws Exception
     */
    public function sendEmail($email, $view, $viewData, $subject)
    {
        try {
            Mail::send($view, $viewData, function ($message) use ($email, $subject) {
                $message->to($email)->subject($subject);
            });
        } catch (Exception $ex) {
            dd("send mail: " . $ex);
            throw new Exception(trans('polls.message.send_mail_fail'));
        }
    }

    public function store($input)
    {
        try {
            DB::beginTransaction();
            $pollId = $this->addInfo($input);

            if (! $pollId || ! ($this->addOption($input, $pollId) && $this->addSetting($input, $pollId))) {
                DB::rollback();

                return false;
            }

            $links =  $this->addLink($pollId, $input);

            if (! $links) {
                DB::rollback();

                return false;
            }

            /*
             * send mail participant
             */
            $participant = $input['participant'];

            if ($participant == config('settings.participant.invite_people')) {
                $members = explode(",", $input['member']);
                $view = config('settings.view.poll_mail');
                $data = [
                    'link' => $links['participant'],
                    'administration' => false,
                ];
                $subject = trans('label.mail.subject');
                $this->sendEmail($members, $view, $data, $subject);
            }

            /*
             * send mail creator
             */
            $creatorView = config('settings.view.poll_mail');
            $email = $input['email'];
            $data = [
                'link' => $links['participant'],
                'administration' => true,
                'linkAdmin' => $links['administration'],
            ];
            $subject = trans('label.mail.subject');
            $this->sendEmail($email, $creatorView, $data, $subject);
            DB::commit();

            return true;
        } catch (Exception $ex) {
            DB::rollback();
            return false;
        }
    }

    /**
     *
     * Admin edit information of a poll
     *
     * @param $input
     * @param $id
     *
     * @return string|\Symfony\Component\Translation\TranslatorInterface
     */
    public function editInfor($input, $id)
    {
        $poll = Poll::with('user')->find($id);

        //data changed
        $data = [];
        $old = [];
        $now = Carbon::now();

        try {
            DB::beginTransaction();

            foreach ($input as $key => $value) {
                if ($key == 'status') {
                    $status = $this->getStatus($poll->status, true);

                    if ($value != $status) {
                        $data[] = [
                            $key => $this->getStatus($value, false),
                        ];
                        $old[] = [
                            $key => $this->getStatus($poll->status, false),
                        ];
                        $poll->status = $value;
                    }
                } elseif ($key == 'type') {
                    $type = $this->getType($poll->multiple, true);

                    if ($value != $type) {
                        $data[] = [
                            $key => $this->getType($poll->multiple, false),
                        ];
                        $old[] = [
                            $key => $poll->multiple,
                        ];
                        $poll->multiple = $value;
                    }
                } elseif ($key == 'name' || $key == 'email' || $key == 'chatwork_id') {
                    if ($value != $poll->user->$key) {
                        $data[] = [
                            $key => $value,
                        ];
                        $old[] = [
                            $key => $poll->user->$key,
                        ];
                        $poll->user->$key = $value;
                    }
                } else {
                    if ($value != $poll->$key) {
                        $data[] = [
                            $key => $value,
                        ];
                        $old[] = [
                            $key => $poll->$key,
                        ];
                        $poll->$key = $value;
                    }
                }
            }

            $poll->save();
            $poll->user->save();

            //If have change about poll, system will send a email to poll creator
            if ($data) {
                $creatorMail = $poll->user->email;

                //send mail to creator
                Mail::send('layouts.mail_notification', compact('data', 'old', 'now'),
                    function ($message) use ($creatorMail) {
                    $message->to($creatorMail)->subject(trans('label.mail.edit_poll.head'));
                });
            }

            $message = trans('polls.message.update_poll_info_success');
            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();
            $message = trans('polls.message.update_poll_info_fail');
        }

        return $message;
    }

    /**
     *
     * Admin edit option lists of poll
     *
     * @param $input
     * @param $id
     *
     * @return string|\Symfony\Component\Translation\TranslatorInterface
     */
    public function editPollOption($input, $id)
    {
        $poll = Poll::with('options')->find($id);
        $now = Carbon::now();
        $options = [];

        try {
            DB::beginTransaction();

            /*
             * REMOVE OPTION
             *
             */
            foreach ($poll->options as $option) {
                if (array_get($input['option'], $option->id)) {
                    $options[] = $option;
                    continue;
                }

                //remove vote of option
                Vote::where('option_id', $option->id)->delete();
                ParticipantVote::where('option_id', $option->id)->delete();

                //delete image of option
                if ($option->image) {
                    $path = public_path() . config('settings.option.path_image') . $option->image;

                    if (File::exists($path)) {
                        File::delete($path);
                    }
                }

                //remove option
                Option::findOrFail($option->id)->delete();
            }

            /*
             *
             * ADD A NEW OPTION: process add option
             *
             */
            $dataOption = [];
            $nameOptionImage = [];

            if ($input['optionImage']) {
                foreach ($input['optionImage'] as $key => $image) {
                    $nameOptionImage['optionImage'][$key] = uniqid(rand(), true) . '.' . $image->getClientOriginalExtension();
                }
            }

            if (count($input['optionText'])) {
                foreach ($input['optionText'] as $key => $option) {
                    $optionImage = array_get($input['optionImage'], $key) ? $input['optionImage'][$key] : null;
                    if ($option && $optionImage) {
                        $dataOption[] = [
                            'poll_id' => $id,
                            'name' => $option,
                            'image' => $nameOptionImage['optionImage'][$key],
                            'created_at' => $now,
                        ];
                    } elseif ($option && ! $optionImage) {
                        $dataOption[] = [
                            'poll_id' => $id,
                            'name' => $option,
                            'image' => null,
                            'created_at' => $now,
                        ];
                    } elseif (! $option && $optionImage) {
                        $dataOption[] = [
                            'poll_id' => $id,
                            'name' => null,
                            'image' => $nameOptionImage['optionImage'][$key],
                            'created_at' => $now,
                        ];
                    }
                }
            }

            if ($dataOption) {
                Option::insert($dataOption);
            }

            //upload image of option
            if (is_array($input['optionImage']) && $input['optionImage']) {
                foreach ($input['optionImage'] as $key => $optionImage) {
                    if (! $optionImage) {
                        continue;
                    }

                    try {
                        $path = public_path() . config('settings.option.path_image');
                        $pathFileOption = '';

                        do {
                            //upload file
                            $fileOption =  uniqid(rand(), true) . '.' . $nameOptionImage['optionImage'][$key];
                            $pathFileOption = $path . $fileOption;
                        } while (File::exists($pathFileOption));

                        $optionImage->move($path, $pathFileOption);
                    } catch (Exception $ex) {
                        throw new Exception(trans('polls.message.upload_image_fail'));
                    }
                }
            }

            /*
             *
             * EDIT A OLD OPTION
             *
             */
            $nameImage = [];
            $newData = [];

            if ($input['image']) {
                foreach ($input['image'] as $key => $image) {
                    $nameImage['image'][$key] = uniqid(rand(), true) . '.' . $image->getClientOriginalExtension();
                }
            }

            //filter option changed lists
            foreach ($options as $option) {
                if (array_get($input['option'], $option->id) && $option->name != $input['option'][$option->id]) {
                    $newData[$option->id][] = [
                        'name' => $input['option'][$option->id]
                    ];
                }

                if (array_get($input['image'], $option->id) && $option->image != $input['image'][$option->id]) {
                    $newData[$option->id][] = [
                        'image' => $nameImage['image'][$option->id]
                    ];
                }
            }

            //handle images
            if ($input['image']) {
                foreach ($input['image'] as $optionId => $image) {
                    try {
                        //remove old file
                        $option = Option::findOrFail($optionId);
                        $oldImagePath = public_path() . config('settings.option.path_image') . $option->image;

                        if (File::exists($oldImagePath)) {
                            File::delete($oldImagePath);
                        }

                        //add new file
                        $path = public_path() . config('settings.option.path_image');
                        $pathFileOption = '';
                        do {
                            //upload file
                            $fileOption =  $nameImage['image'][$optionId];
                            $pathFileOption = $path . $fileOption;
                        } while (File::exists($pathFileOption));

                        $image->move($path, $pathFileOption);
                    } catch (Exception $ex) {
                        throw new Exception(trans('polls.message.upload_image_fail'));
                    }
                }
            }

            //update data
            foreach ($newData as $id => $fields) {
                foreach ($fields as $field) {
                    Option::findOrFail($id)->update($field);
                }
            }

            DB::commit();
            $message = trans('polls.message.update_option_success');
        } catch (Exception $ex) {
            DB::rollBack();
            $message = trans('polls.message.update_option_fail');
        }

        return $message;
    }

    public function editPollSetting($input, $id)
    {
        $poll = Poll::with('settings')->find($id);
        $now = Carbon::now();

        try {
            DB::beginTransaction();

            /* ---------------------------------
             *              SETTING
             *-----------------------------------*/
            // remove setting
            foreach ($poll->settings as $setting) {
                if (! in_array($setting->key, $input['setting'])) {
                    Setting::find($setting->id)->delete();
                }
            }

            // add setting
            $oldSetting = $poll->settings->pluck('value', 'key')->toArray();
            $newData = [];

            foreach ($input['setting'] as $inputName => $setting) {
                $value = null;

                if (isset($input['value'][$inputName]) && $input['value'][$inputName]) {
                    $value = $input['value'][$inputName];

                    if ($setting == config('settings.setting.set_password')) {
                        $value = bcrypt($input['value'][$inputName]);
                    }
                }

                if (empty($oldSetting[$setting])) {
                    $newData[] = [
                        'poll_id' => $id,
                        'key' => $setting,
                        'value' => $value,
                        'created_at' => $now,
                    ];
                }
            }

            if ($newData) {
                Setting::insert($newData);
            }

            // edit value of setting
            $settingId = $poll->settings->pluck('id', 'key')->toArray();
            foreach ($input['setting'] as $name => $key) {
                if (empty($input['value'][$name])) {
                    continue;
                }

                $value = ($key == config('settings.setting.set_password')) ? bcrypt($input['value'][$name]) : $input['value'][$name];

                if ($oldSetting[$key] != $input['value'][$name]) {
                    Setting::find($settingId[$key])->update([
                        'value' => $value
                    ]);
                }
            }

            DB::commit();

            /* ---------------------------------
             *              PARTICIPANT
             *-----------------------------------*/
            if ($input['participant']) {
                $participants = explode(',', $input['participant']);
                $emails = [];

                foreach ($participants as $participant) {
                    if (filter_var($participant, FILTER_VALIDATE_EMAIL)) {
                        $emails[] = $participant;
                    }
                }

                if ($emails) {
                    $settingToken = Setting::where([
                        'poll_id' => $id,
                        'key' => config('settings.setting.custom_link')
                    ])->first();

                    if ($settingToken) {
                        $token = $settingToken->value;
                    } else {
                        $pollToken = Link::where([
                            'poll_id'=> $id,
                            'link_admin'=> config('settings.link_poll.vote'),
                        ])->first();

                        if ($pollToken) {
                            $token = $pollToken->token;
                        } else {
                            $token = str_random(16);
                            Link::firstOrCreate([
                                'poll_id' => $id,
                                'token' => $token,
                                'link_admin' => config('settings.link_poll.vote'),
                            ]);
                        }
                    }

                    $linkMail = url("/") . config('settings.email.link_vote') . $token;

                    //send mail for member
                    Mail::send('layouts.participant-mail', [
                        'link' => $linkMail,
                    ], function ($message) use ($emails) {
                        $message->to($emails)->subject(trans('label.mail.subject'));
                    });
                }

            }

            $message = trans('polls.message.update_setting_success');
        } catch (Exception $ex) {
            DB::rollBack();
            $message = trans('polls.message.update_setting_fail');
        }

        return $message;
    }

    public function delete($ids)
    {
        $poll = Poll::with(
            'options', 'settings', 'links', 'activities', 'comments', 'links'
        )->findOrFail($ids);

        try {
            DB::beginTransaction();
            $optionId = Option::where('poll_id', $ids)->pluck('id')->toArray();

            /**
             * delete vote option of user
             */
            Vote::whereIn('option_id', $optionId)->delete();

            /**
             * delete vote option of participant
             */
            ParticipantVote::whereIn('option_id', $optionId)->delete();

            /**
             * delete option
             */
            $poll->options()->delete();

            /**
             * delete setting
             */
            $poll->settings()->delete();

            /**
             * delete link
             */
            $poll->links()->delete();

            /**
             * delete comment
             */
            $poll->comments()->delete();

            /**
             * delete activity
             */
            $poll->activities()->delete();

            /**
             * delete poll
             */
            $poll->delete();
            DB::commit();
            $message = trans('polls.message.delete_poll_success');
        } catch (Exception $e) {
            DB::rollBack();
            $message = trans('polls.message.delete_poll_fail');
        }

        return $message;
    }

    public function getStatus($status, $isKey)
    {
        $config = config('settings.status');
        $trans = trans('polls.label');

        if ($isKey) {
            //return result status key: 0, 1
            if ($status =  $trans['poll_opening'] || $status == $trans['poll_opening']) {
                return $config['open'];
            }

            return $config['close'];
        }

        //return result type text: closed, opening
        if ($status == $trans['poll_opening'] || $status == $config['open']) {
            return $trans['open'];
        }

        return $trans['close'];
    }

    public function getType($type, $isKey)
    {
        $config = config('settings.type_poll');
        $trans = trans('polls.label');

        if ($isKey) {
            //return result type key: 0, 1
            return ($type == $trans['multiple_choice'] ? $config['multiple_choice']: $config['single_choice']);
        }

        //return result type text: multiple, single
        return ($type == $config['multiple_choice'] ? $trans['multiple_choice']: $trans['single_choice']);
    }

}
