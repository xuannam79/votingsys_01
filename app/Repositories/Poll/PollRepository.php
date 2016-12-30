<?php

namespace App\Repositories\Poll;

use App\Models\Activity;
use App\Models\Link;
use App\Models\Option;
use App\Models\Participant;
use App\Models\ParticipantVote;
use App\Models\Poll;
use App\Models\Setting;
use App\Models\User;
use App\Models\Vote;
use Auth;
use Carbon\Carbon;
use File;
use Exception;
use DB;
use App\Repositories\BaseRepository;
use Mail;
use Session;

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

        return $participantPolls->unique();
    }

    public function getClosedPolls()
    {
        $currentUserId = auth()->user()->id;

        return $this->model->where([
            'user_id' =>  $currentUserId,
            'status' => false
        ])->with('activities')->orderBy('id', 'DESC')->get();
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


    public function getDataPollSystem()
    {

        //get data to send javascript file
        $jsonData = json_encode([
            'message' => trans('polls.message_client'),
            'config' => [
                'length' => config('settings.length_poll'),
                'setting' => config('settings.setting'),
                'link' => url('/') . config('settings.email.link_vote'),
            ],
            'view' => [
                'option' => view('layouts.poll_option')->render(),
                'email' => view('layouts.poll_email')->render(),
            ],
            'oldInput' => session("_old_input"),
        ]);

        //get data to send view file
        $typePollConfig = config('settings.type_poll');
        $settingPollConfig = config('settings.setting');
        $pollTrans = trans('polls.label');
        $viewData = [
            'types' => [
                $typePollConfig['single_choice'] => $pollTrans['single_choice'],
                $typePollConfig['multiple_choice'] => $pollTrans['multiple_choice'],
            ],
            'settings' => [
                $settingPollConfig['required'] => $pollTrans['setting']['required'],
                $settingPollConfig['hide_result'] => $pollTrans['setting']['hide_result'],
                $settingPollConfig['custom_link'] => $pollTrans['setting']['custom_link'],
                $settingPollConfig['set_limit'] => $pollTrans['setting']['set_limit'],
                $settingPollConfig['set_password'] => $pollTrans['setting']['set_password'],
            ],
        ];

        return compact('jsonData', 'viewData');
    }


    /**
     *
     * Get id of user when create a poll
     *
     * @param null $email
     * @return string
     */
    private function getUserId($email = null) {
        $currentUser = auth()->user();

        if ($currentUser) {
            return ($currentUser->email) ? $currentUser->id : '';
        }

        $user = User::where('email', $email)->first();

        return ($user) ? $user->id : '';
    }

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
        try {
            $userId = $this->getUserId($input['email']);
            $now = Carbon::now();

            $pollId = Poll::insertGetId([
                'user_id' => ($userId) ? $userId : null,
                'title' => $input['title'],
                'description' => ($input['description']) ? $input['description'] : null,
                'location' => ($input['location']) ? $input['location'] : null,
                'multiple' => $input['type'],
                'created_at' => $now,
                'date_close' => ($input['closingTime']) ? $input['closingTime'] : null,
                'name' => ($userId) ? null : $input['name'],
                'email' => ($userId) ? null : $input['email'],
            ]);

            return $pollId;
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
    private function createFileName($arrInputImage)
    {
        $imageNames = [];

        if ($arrInputImage) {
            foreach ($arrInputImage as $key => $image) {
                do {
                    $imageNames['optionImage'][$key] = uniqid(rand(), true) . '.' . $image->getClientOriginalExtension();
                    $path = public_path() . config('settings.option.path_image') . $imageNames['optionImage'][$key];
                } while (File::exists($path));
            }
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
    private function updateImage($images, $imageNames, $oldImages = [])
    {
        try {
            /* delete old image */
            if (is_array($oldImages) && $oldImages) {
                foreach ($oldImages as $image) {
                    $path = public_path() . config('settings.option.path_image') . $image;
                    if (File::exists($path)) {
                        File::delete($path);
                    }
                }
            }

            /* upload new image */
            if ($images) {
                $pathTo = public_path() . config('settings.option.path_image');

                foreach ($images as $key => $image) {
                    $pathFrom = $pathTo . $imageNames['optionImage'][$key];
                    $image->move($pathTo, $pathFrom);
                }
            }
        } catch (Exception $ex) {
            throw new Exception(trans('polls.message.upload_image_fail'));
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
            $settingChilds = $input['setting_child'];
            $value = $input['value'];
            $dataSettingInserted = [];
            $now = Carbon::now();

            if ($settings) {
                foreach ($settings as $setting) {
                    if ($setting == config('settings.setting.required')) {
                        $dataSettingInserted[] = [
                            'poll_id' => $pollId,
                            'key' => $settingChilds['required'],
                            'value' => null,
                            'created_at' => $now,
                        ];
                    } else {
                        $dataSettingInserted[] = [
                            'poll_id' => $pollId,
                            'key' => $setting,
                            'value' => $this->getValueOfSetting($setting, $value),
                            'created_at' => $now,
                        ];
                    }
                }
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
            return $values['password'];
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

            if ($input['setting']
                && array_key_exists(config('settings.setting.custom_link'), $input['setting'])
                && $input['value']['link']) {
                $participantLink = $input['value']['link'];
            }

            /* insert link of participant */
            Link::create([
                'poll_id' => $pollId,
                'token' => $participantLink,
                'link_admin' => config('settings.link_poll.vote'),
            ]);

            /* insert link of administration */
            Link::create([
                'poll_id' => $pollId,
                'token' => $administrationLink,
                'link_admin' => config('settings.link_poll.admin'),
            ]);

            $linkReturn = [
                'participant' => $participantLink,
                'administration' => $administrationLink,
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
            Mail::queue($view, $viewData, function ($message) use ($email, $subject) {
                $message->to($email)->subject($subject);
            });
        } catch (Exception $ex) {
            throw new Exception(trans('polls.message.send_mail_fail'));
        }
    }

    public function addDuplicateOption($input, $pollId)
    {
        try {
            $now = Carbon::now();
            $oldImage = $input['oldImage'];
            $optionOldImage = $input['optionOldImage'];
            $nameOldImage = $this->createFileName($optionOldImage);
            $optionImage = $input['optionImage'];
            $nameImage = $this->createFileName($optionImage);
            $optionText = $input['optionText'];
            $dataInsert = [];

            foreach ($optionText as $key => $value) {
                if (! $value) {
                    continue;
                }
                if ($optionOldImage && array_key_exists($key, $optionOldImage)) {
                    $dataInsert[] = [
                        'poll_id' => $pollId,
                        'name' => $value,
                        'image' => $nameOldImage['optionImage'][$key],
                        'created_at' => $now,
                    ];
                } elseif ($oldImage && array_key_exists($key, $oldImage)) {
                    $dataInsert[] = [
                        'poll_id' => $pollId,
                        'name' => $value,
                        'image' => $oldImage[$key],
                        'created_at' => $now,
                    ];
                } else {
                    $dataInsert[] = [
                        'poll_id' => $pollId,
                        'name' => $value,
                        'image' => ($nameImage && array_key_exists($key, $nameImage['optionImage'])) ?
                                    $nameImage['optionImage'][$key] : null,
                        'created_at' => $now,
                    ];
                }
            }

            if ($dataInsert) {
                Option::insert($dataInsert);
                $this->updateImage($optionOldImage, $nameOldImage);
                $this->updateImage($optionImage, $nameImage);
            }

            return true;
        } catch (Exception $ex) {
            return false;
        }
    }

    public function store($input)
    {
        try {
            DB::beginTransaction();
            $pollId = $this->addInfo($input);

            if ($input['page'] == 'duplicate') {
                if (! $pollId || ! ($this->addDuplicateOption($input, $pollId) && $this->addSetting($input, $pollId))) {
                    DB::rollback();

                    return false;
                }
            } else {
                if (! $pollId || ! ($this->addOption($input, $pollId) && $this->addSetting($input, $pollId))) {
                    DB::rollback();

                    return false;
                }
            }

            $links =  $this->addLink($pollId, $input);

            if (! $links) {
                DB::rollback();

                return false;
            }

            $poll = Poll::with('user')->find($pollId);

            /*
             * send mail participant
             */
            $password = false;

            if (count($input['setting'])) {
                $password = in_array(config('settings.setting.set_password'), $input['setting'])
                            ? $input['value']['password'] : false;
            }

            $dataRtn = [
                'poll' => $poll,
                'link' => $links,
            ];

            if ($input['member']) {
                $members = explode(",", $input['member']);
                $view = config('settings.view.participant_mail');
                $data = [
                    'linkVote' => $poll->getUserLink(),
                    'poll' => $poll,
                    'password' => $password,
                ];
                $subject = trans('label.mail.participant_vote.subject');
                $this->sendEmail($members, $view, $data, $subject);
            }

            /*
             * send mail creator
             */
            $creatorView = config('settings.view.poll_mail');
            $emailOfCreator = $input['email'];
            $data = [
                'userName' => $input['name'],
                'linkVote' => $poll->getUserLink(),
                'linkAdmin' => $poll->getAdminLink(),
                'poll' => $poll,
                'password' => $password,
            ];
            $subject = trans('label.mail.create_poll.subject');
            $this->sendEmail($emailOfCreator, $creatorView, $data, $subject);
            DB::commit();

            return $dataRtn;
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

        if ($poll->user_id) {
            $users = User::where('email', $input['email'])->where('email', '<>', $poll->user->email)->count();

            if ($users) {
                return trans('polls.message.email_exists');
            }
        }

        //data changed
        $data = [];
        $old = [];
        $now = Carbon::now();

        try {
            DB::beginTransaction();

            foreach ($input as $key => $value) {
                if ($key == 'type') {
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
                } else {
                    if ($poll->user_id && ($key == 'name' || $key == 'email')) {
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
            }

            $poll->save();
            if ($poll->user_id) {
                $poll->user->save();
            }

            //If have change about poll, system will send a email to poll creator
            if ($data) {
                $creatorMail = ($poll->user_id) ? $poll->user->email : $poll->email;
                $creatorName = ($poll->user_id) ? $poll->user->name : $poll->name;

                //send mail to creator
                Mail::queue('layouts.mail_notification', compact('data', 'old', 'now', 'creatorName'),
                    function ($message) use ($creatorMail) {
                    $message->to($creatorMail)->subject(trans('label.mail.edit_poll.subject'));
                });
            }

            Activity::create([
                'poll_id' => $id,
                'user_id' => (auth()->user()) ? auth()->user()->id : null,
                'type' => config('settings.activity.edit_poll'),
                'name' => null,
            ]);

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
        $pollId = $id;
        $now = Carbon::now();
        $options = [];

        try {
            $oldOptions = $poll->options;
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
                    $path = public_path() . config('settings.option.path_image');
                    $pathFileOption = '';

                    do {
                        //upload file
                        $fileOption =  uniqid(rand(), true) . '.' . $image->getClientOriginalExtension();
                        $pathFileOption = $path . $fileOption;
                        $nameOptionImage['optionImage'][$key] = $fileOption;
                    } while (File::exists($pathFileOption));
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
                        $pathFileOption = $path . $nameOptionImage['optionImage'][$key];;
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
                        $option = Option::find($optionId);
                        if ($option) {
                            $oldImagePath = public_path() . config('settings.option.path_image') . $option->image;

                            if (File::exists($oldImagePath)) {
                                File::delete($oldImagePath);
                            }
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
            $newPoll = Poll::with('options', 'user')->findOrFail($pollId);
            $newOptions = $newPoll->options;

            if ($newPoll->user_id) {
                $creatorName = $newPoll->user->name;
                $creatorMail = $newPoll->user->email;
            } else {
                $creatorName = $newPoll->name;
                $creatorMail = $newPoll->email;
            }

            //send mail to creator
            Mail::queue(config('settings.view.mail_edit_option'), compact('oldOptions', 'newOptions', 'now', 'creatorName'),
                function ($message) use ($creatorMail) {
                    $message->to($creatorMail)->subject(trans('label.mail.edit_option.subject'));
            });
            Activity::create([
                'poll_id' => $id,
                'user_id' => (auth()->user()) ? auth()->user()->id : null,
                'type' => config('settings.activity.edit_poll'),
                'name' => null,
            ]);
            $message = trans('polls.message.update_option_success');
        } catch (Exception $ex) {
            DB::rollBack();
            $message = trans('polls.message.update_option_fail');
        }

        return $message;
    }

    public function editPollSetting($input, $id)
    {
        $poll = $this->model->with('settings')->find($id);
        $pollId = $id;
        $now = Carbon::now();
        try {
            $oldSettings = $this->showSetting($poll->settings);
            $poll->settings()->delete();
            $this->addSetting($input, $id);

            if (array_key_exists(config('settings.setting.custom_link'), $input['setting'])) {
                Link::where([
                    'poll_id' => $id,
                    'link_admin' => config('settings.link_poll.vote'),
                ])->update(['token' => $input['value']['link']]);
            }

            $newPoll = Poll::with('user', 'settings')->findOrFail($pollId);
            $newSettings = $this->showSetting($newPoll->settings);
            if ($poll->user_id) {
                $creatorName = $newPoll->user->name;
                $creatorMail = $newPoll->user->email;
            } else {
                $creatorName = $newPoll->name;
                $creatorMail = $newPoll->email;
            }

            //send mail to creator
            Mail::queue(config('settings.view.mail_edit_setting'), compact('newSettings', 'oldSettings', 'now', 'creatorName'),
                function ($message) use ($creatorMail) {
                    $message->to($creatorMail)->subject(trans('label.mail.edit_setting.subject'));
                });
            Activity::create([
                'poll_id' => $id,
                'user_id' => (auth()->user()) ? auth()->user()->id : null,
                'type' => config('settings.activity.edit_poll'),
                'name' => null,
            ]);
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
            if ($status ==  $trans['opening'] || $status == $trans['poll_opening']) {
                return $config['open'];
            }

            return $config['close'];
        }

        //return result type text: closed, opening
        if ($status == $trans['poll_opening'] || $status == $config['open']) {
            return $trans['opening'];
        }

        return $trans['closed'];
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

    /*
     * get vote first of poll
     */
    public function getTimeFirstVote($poll) {
        $now = Carbon::now();
        $timeFirstVotePoll = $now;

        foreach ($poll->options as $option) {
            $voteFirst = Vote::where('option_id', $option->id)->orderBy('created_at', 'asc')->get()->first();
            $participantFirst = ParticipantVote::where('option_id', $option->id)->orderBy('created_at', 'asc')->get()->first();
            $userVoteFirst = ($voteFirst) ? $voteFirst->created_at : $timeFirstVotePoll;
            $participantVoteFirst = ($participantFirst) ? $participantFirst->created_at : $timeFirstVotePoll;
            $timeFirstVoteOption = (strcmp($userVoteFirst, $participantVoteFirst) < 0) ? $userVoteFirst : $participantVoteFirst;
            $timeFirstVotePoll = ($timeFirstVoteOption < $timeFirstVotePoll) ? $timeFirstVoteOption: $timeFirstVotePoll;
        }
        return ($timeFirstVotePoll == $now) ? '' : $timeFirstVotePoll;
    }

    /*
    * get vote last of poll
    */
    public function getTimeLastVote($poll)
    {
        $timeLastVotePoll = $poll->created_at;

        foreach ($poll->options as $option) {
            $voteLast = Vote::where('option_id', $option->id)->orderBy('created_at', 'desc')->get()->first();
            $participantLast = ParticipantVote::where('option_id', $option->id)->orderBy('created_at', 'desc')->get()->first();
            $userVoteLast = ($voteLast) ? $voteLast->created_at : $timeLastVotePoll;
            $participantVoteLast = ($participantLast) ? $participantLast->created_at : $timeLastVotePoll;
            $timeLastVoteOption = (strcmp($userVoteLast, $participantVoteLast) < 0) ? $participantVoteLast : $userVoteLast;
            $timeLastVotePoll = ($timeLastVoteOption > $timeLastVotePoll) ? $timeLastVoteOption: $timeLastVotePoll;
        }

        return ($timeLastVotePoll == $poll->created_at) ? '' : $timeLastVotePoll;
    }

    /*
     * get total vote of poll
     */
    public function getTotalVotePoll($poll)
    {
        $voteTotal = 0;

        foreach ($poll->options as $option) {
            $voteTotal += $option->countVotes();
        }

        return $voteTotal;
    }

    public function getOptionLargestVote($poll)
    {
        $numberOfLargestVote = 0;
        $largestVote = null;

        foreach ($poll->options as $option) {
            if ($option->countVotes() > $numberOfLargestVote) {
                $numberOfLargestVote = $option->countVotes();
                $largestVote = $option;
            }
        }

        $optionLargestVote = [];

        foreach ($poll->options as $option) {
            if ($option->countVotes() == $numberOfLargestVote) {
                $optionLargestVote[] = $option;
            }
        }

        return [
            'number' => $numberOfLargestVote,
            'option' => $optionLargestVote,
        ];
    }

    /*
    * get option have number vote least
    */
    public function getOptionLeastVote($poll)
    {
        $numberOfLeastVote = $this->getTotalVotePoll($poll);
        $leastVote = null;

        foreach ($poll->options as $option) {
            if ($option->countVotes() < $numberOfLeastVote) {
                $numberOfLeastVote = $option->countVotes();
                $leastVote = $option;
            }
        }

        $optionLeastVote = [];

        foreach ($poll->options as $option) {
            if ($option->countVotes() == $numberOfLeastVote) {
                $optionLeastVote[] = $option;
            }
        }

        return [
            'number' => $numberOfLeastVote,
            'option' => $optionLeastVote,
        ];
    }

    /*
     * get option return table
     */
    public function getDataTableResult($poll)
    {
        $dataTableResult = [];

        foreach ($poll->options as $option) {

            //Get vote last of option
            $voteLast = Vote::where('option_id', $option->id)->get()->last();
            $participantLast = ParticipantVote::where('option_id', $option->id)->get()->last();
            $userVoteLast = ($voteLast) ? $voteLast->created_at : '';
            $participantVoteLast = ($participantLast) ? $participantLast->created_at : '';

            $dataTableResult[] = [
                'name' => $option->name,
                'image' => $option->showImage(),
                'numberOfVote' => $option->countVotes(),
                'lastVoteDate' => (strcmp($userVoteLast, $participantVoteLast) < 0) ? $participantVoteLast : $userVoteLast,
                'option_id' => $option->id,
            ];
        }

        return $dataTableResult;
    }

    public function showSetting($settings)
    {
        $dataRtn = [];
        $trans = trans('polls.label.setting');
        $config = config('settings.setting');

        foreach ($settings as $setting) {
            switch ($setting->key) {
                case $config['required_email']:
                    $dataRtn[] = [
                        $trans['required'] . ': ' . $trans['required_email'] => null
                    ];
                    break;
                case $config['required_name']:
                    $dataRtn[] = [
                        $trans['required'] . ': ' . $trans['required_name'] => null
                    ];
                    break;
                case $config['required_name_and_email']:
                    $dataRtn[] = [
                        $trans['required'] . ': ' . $trans['required_name_and_email'] => null
                    ];
                    break;
                case $config['hide_result']:
                    $dataRtn[] = [
                        $trans['hide_result'] => null
                    ];
                    break;
                case $config['custom_link']:
                    $dataRtn[] = [
                        $trans['custom_link'] => $setting->value
                    ];
                    break;
                case $config['set_limit']:
                    $dataRtn[] = [
                        $trans['set_limit'] => $setting->value
                    ];
                    break;
                case $config['set_password']:
                    $dataRtn[] = [
                        $trans['set_password'] => $setting->value
                    ];
                    break;
            }
        }

        return $dataRtn;
    }

    public function sendMailAgain($poll, $link, $password)
    {
        $creatorView = config('settings.view.poll_mail');
        $email = ($poll['user_id']) ? $poll['user']['email'] : $poll['email'];
        $link = url('/') . config('settings.link_poll.link_vote');
        $linkAdmin = $link . str_random(config('settings.length_poll.link'));
        $linkVote = $link . str_random(config('settings.length_poll.link'));

        foreach ($poll['links'] as $links) {
            if ($links['link_admin'] == config('settings.link_poll.admin')) {
                $linkAdmin = $link . $links['token'];
            } else {
                $linkVote = $link . $links['token'];
            }
        }

        $data = [
            'userName' => ($poll['user_id']) ? $poll['user']['name'] : $poll['name'],
            'title' => $poll['title'],
            'type' => $this->getType($poll['multiple'], false),
            'location' => $poll['location'],
            'description' => $poll['description'],
            'closeDate' => $poll['date_close'],
            'createdAt' => $poll['created_at'],
            'linkVote' => $linkVote,
            'linkAdmin' => $linkAdmin,
            'password' => $password,
        ];
        $subject = trans('label.mail.create_poll.subject');
        $this->sendEmail($email, $creatorView, $data, $subject);
    }

    private function countTotalVote($poll)
    {
        $totalVote = 0;

        foreach ($poll->options as $option) {
            $totalVote += $option->countVotes();
        }

        return $totalVote;
    }

    public function getNameOptionToDrawChart($poll, $isHasImage)
    {
        $nameOfOptions = [];
        $lengthDefault = config('settings.length_poll.name_option');
        $lengthDefaultNotImage = config('settings.length_poll.name_option_not_image');

        foreach ($poll->options as $option) {
            if ($isHasImage) {
                $nameOption = (strlen($option->name) > $lengthDefault) ? str_limit($option->name, $lengthDefault)  : $option->name;
            } else {
                $nameOption = (strlen($option->name) > $lengthDefaultNotImage) ? str_limit($option->name, $lengthDefaultNotImage)  : $option->name;
            }

            $countOption = $option->countVotes();
            if ($countOption > 0) {
                $nameOfOptions[] = [
                    view('layouts.chart_option_data', [
                        'isHasImage' => $isHasImage,
                        'imagePath' => $option->showImage(),
                        'optionName' => $nameOption,
                        'size' => $this->getSizeChart($poll)['sizeImage'],
                        'marginLeft' => $this->getSizeChart($poll)['marginImage'],
                        'optionFullName' => $option->name,
                    ])->render()
                ];
            }
        }

        return $nameOfOptions;
    }

    public function getDataToDrawPieChart($poll, $isHasImage)
    {
        $totalVote = $this->countTotalVote($poll);
        $optionRateBarChart = [];
        $nameOption = false;

        if ($totalVote) {
            foreach ($poll->options as $option) {
                $countOption = $option->countVotes();
                if ($countOption > 0) {
                    if (Session::get('locale') != "ja") {
                        $nameOption = $this->insertTagBreakInOptionName($option->name);
                    }

                    if ($nameOption) {
                        if ($isHasImage) {
                            $optionRateBarChart[] = [
                                '<img src="' . $option->showImage() . '" class="image-option-poll"><span class="name-option-poll">' . $nameOption .'</span>', $countOption
                            ];
                        } else {
                            $optionRateBarChart[] = [
                                '<p>' . $nameOption .'</p>', $countOption
                            ];
                        }

                    } else {
                        if ($isHasImage) {
                            $optionRateBarChart[] = [
                                '<img src="' . $option->showImage() . '" class="image-option-poll"><span class="name-option-poll">' . $option->name .'</span>', $countOption
                            ];
                        } else {
                            $optionRateBarChart[] = [
                                '<p>' . $option->name .'</p>', $countOption
                            ];
                        }

                    }
                }
            }
        } else {
            $optionRateBarChart = null;
        }

        return $optionRateBarChart;
    }

    private function insertTagBreakInOptionName($optionName)
    {
        if (strlen($optionName) < config('settings.length_poll.name_option')) {
            return false;
        }

        $result = '';
        $position = 0;

        for ($index = 0; $index < strlen($optionName); $index++) {
            if ($index != 0 && $index % (config('settings.length_poll.name_option')) == 0) {
                $result .= substr($optionName, $position, config('settings.length_poll.name_option')) . '<br>';

                $position = $index;
            }

            if ($index == (strlen($optionName) - 1)) {
                $result .= substr($optionName, $position, strlen($optionName) - $position);

                break;
            }
        }

        if (strlen($optionName) % config('settings.length_poll.name_option') == 0) {
            $result .=  '<br>';
        }

      return $result;
    }

    public function getSizeChart($poll)
    {
        $dataSizeRtn = [];
        $countVotedOption = $poll->countVotedOption();
        $numberofVoteConfig = config('settings.chart.number');
        $sizeImage = config('settings.chart.size');
        $marginImage = config('settings.chart.margin_left');
        $fontSize = config('settings.chart.font_size');

        if ($countVotedOption >= $numberofVoteConfig['lager']) {
            $dataSizeRtn = [
                'sizeImage' => $sizeImage ['small'],
                'marginImage' => $marginImage ['small'],
                'fontSize' => $fontSize ['small'],
            ];
        } elseif ($countVotedOption >= $numberofVoteConfig['middle']) {
            $dataSizeRtn = [
                'sizeImage' => $sizeImage ['middle'],
                'marginImage' => $marginImage ['middle'],
                'fontSize' => $fontSize ['middle'],
            ];
        } else {
            $dataSizeRtn = [
                'sizeImage' => $sizeImage ['lager'],
                'marginImage' => $marginImage ['lager'],
                'fontSize' => $fontSize ['lager'],
            ];
        }

       return $dataSizeRtn;
    }
}

