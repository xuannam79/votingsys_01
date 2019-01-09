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
use Intervention\Image\Facades\Image;

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
                $settingPollConfig['parent_hide_result'] => $pollTrans['setting']['parent_hide_result'],
                $settingPollConfig['disable_voting'] => $pollTrans['setting']['disable_voting'],
                $settingPollConfig['custom_link'] => $pollTrans['setting']['custom_link'],
                $settingPollConfig['set_limit'] => $pollTrans['setting']['set_limit'],
                $settingPollConfig['set_password'] => $pollTrans['setting']['set_password'],
            ],
            'configOptions' => [
                $settingPollConfig['allow_add_option'] => $pollTrans['setting']['allow_new_option'],
                $settingPollConfig['allow_edit_vote_of_poll'] => $pollTrans['setting']['allow_edit_vote_of_poll'],
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
                $img = Image::make($image);

                // Get Extension Image
                $extensionImg = is_string($image) ? getExtension($img->mime()) : $image->getClientOriginalExtension();
                $filename = uniqid(time(), true) . '.' . $extensionImg;

                $imageNames['optionImage'][$key] = uniqid(time(), true) . '.' . $filename;
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
                foreach ($images as $key => $image) {
                    $img = Image::make($image);
                    // resize the image to a height of 350 and constrain aspect ratio (auto width)
                    $img->resize(null, 350, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });

                    $pathFrom = config('settings.option.path_image') . $imageNames['optionImage'][$key];
                    $img->save(ltrim($pathFrom, '/'));
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
    public function addOption($input, $pollId, $getAddNewOption = false)
    {
        DB::beginTransaction();
        try {
            $options = $input['optionText'];
            $images = $input['optionImage'];
            $descriptions = $input['optionDescription'];

            if ($images) {
                foreach ($images as $key => $value) {
                    if (!$value) {
                        unset($images[$key]);
                    }
                }
            }

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
                        'description' => $descriptions[$key] ?: null,
                    ];
                }
            }

            $this->updateImage($images, $imageNames);

            if ($dataOptionInserted && $getAddNewOption) {
                DB::commit();

                return $this->model->find($pollId)->options()->createMany($dataOptionInserted);
            }

            Option::insert($dataOptionInserted);
            DB::commit();

            return true;
        } catch (Exception $ex) {
            DB::rollBack();

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
                    } elseif ($setting == config('settings.setting.parent_hide_result')) {
                        $dataSettingInserted[] = [
                            'poll_id' => $pollId,
                            'key' => $settingChilds['parent_hide_result'],
                            'value' => null,
                            'created_at' => $now,
                        ];
                    } else {
                        $dataSettingInserted[] = [
                            'poll_id' => $pollId,
                            'key' => $setting,
                            'value' => $this->getValueOfSetting($setting, $value, $settingChilds),
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
    public function getValueOfSetting($setting, $values, $settingChilds)
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

        if ($setting == $config['add_type_mail']) {
            if ($settingChilds['required'] == $config['required_email']) {
                return $values['listEmail'];
            }

            return $values['typeEmail'];
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

            if ($input['setting'] && count($input['setting'])) {
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

        $settings = $poll->settings->filter(function ($setting) {
            return ($setting->key == config('settings.setting.allow_add_option')
                    || $setting->key == config('settings.setting.allow_edit_vote_of_poll'));
        })->each(function ($item) {
            $item->delete();
        });

        if (count($input['setting']) && $settings->isEmpty()) {
            $getData = function ($value) {
                return ['key' => $value];
            };

            $data = array_map($getData, $input['setting']);

            $poll->settings()->createMany(array_values($data));
        }

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
                            'description' => $input['optionDescription'][$key],
                        ];
                    } elseif ($option && ! $optionImage) {
                        $dataOption[] = [
                            'poll_id' => $id,
                            'name' => $option,
                            'image' => null,
                            'created_at' => $now,
                            'description' => $input['optionDescription'][$key],
                        ];
                    } elseif (! $option && $optionImage) {
                        $dataOption[] = [
                            'poll_id' => $id,
                            'name' => null,
                            'image' => $nameOptionImage['optionImage'][$key],
                            'created_at' => $now,
                            'description' => $input['optionDescription'][$key],
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
                        $pathFileOption = $path . $nameOptionImage['optionImage'][$key];
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
                        'name' => $input['option'][$option->id],
                    ];
                }

                if (in_array($option->id, $input['imageOptionDelete'])) {
                    $pathDelete = public_path() . config('settings.option.path_image') . $option->image;

                    if (File::exists($pathDelete)) {
                        File::delete($pathDelete);
                    }

                    $newData[$option->id][] = [
                        'image' => null,
                    ];
                }

                if (array_get($input['image'], $option->id) && $option->image != $input['image'][$option->id]) {
                    $newData[$option->id][] = [
                        'image' => $nameImage['image'][$option->id],
                    ];
                }

                if (array_get($input['oldOptionDescription'], $option->id)
                    && $option->description != $input['oldOptionDescription'][$option->id]) {
                    $newData[$option->id][] = [
                        'description' => $input['oldOptionDescription'][$option->id],
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
            DB::beginTransaction();
            $oldSettings = $this->showSetting($poll->settings);
            $poll->settings()->delete();
            $this->addSetting($input, $id);

            if (!empty($input['setting']) && array_key_exists(config('settings.setting.custom_link'), $input['setting'])) {
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
            DB::commit();
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
            $userLast = $option->users->last();

            $participantLast = $option->participants
                ->reject(function ($participant) {
                    return get_class($participant) === User::class;
                })->last();

            $userVoteLast = $userLast ? $userLast->pivot->created_at : '';

            $participantVoteLast = $participantLast ? $participantLast->pivot->created_at : '';

            $dataTableResult[] = [
                'name' => $option->name,
                'image' => $option->showImage(),
                'numberOfVote' => $option->countVotes(),
                'lastVoteDate' => (strcmp($userVoteLast, $participantVoteLast) < 0) ? $participantVoteLast : $userVoteLast,
                'option_id' => $option->id,
                'listVoter' => $option->listVoter(),
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
            // if ($countOption > 0) {
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
            // }
        }

        return $nameOfOptions;
    }

    public function getDataToDrawPieChart($poll, $isHasImage)
    {
        $totalVote = $this->countTotalVote($poll);
        $optionRateBarChart = [];

        if ($totalVote) {
            foreach ($poll->options as $option) {
                $countOption = $option->countVotes();
                if ($countOption > 0) {
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
        } else {
            $optionRateBarChart = null;
        }

        return $optionRateBarChart;
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

    public function checkIfEmailVoterExist($input)
    {
        $poll = $this->model->find($input['pollId']);

        $emailVote = $input['emailVote'];

        $emailIgnore = isset($input['emailIgnore']) ? $input['emailIgnore'] : false;

        if ($poll) {
            $poll->load('options.users', 'options.participants');

            return $poll->options->map(function ($option) {
                return $option->users->merge($option->participants);
            })
            ->flatten()
            ->reject(function ($voter) use ($emailIgnore) {
                return $emailIgnore && $voter->email == $emailIgnore;
            })
            ->contains('email', $emailVote);
        }

        return false;
    }

    public function editVoted($idPoll, $input)
    {
        if (!$input || !$idPoll) {
            return false;
        }

        DB::beginTransaction();
        try {
            $poll = $this->model->find($idPoll)->load('options');
            $images = isset($input['optionImage']) ? $input['optionImage'] : null;
            $imagesOption = isset($input['optionDeleteImage']) ? $input['optionDeleteImage'] : null;
            $textOptions = $input['optionText'];

            $idsVoted = [];

            if ($images) {
                foreach ($images as $key => $value) {
                    if (!$value) {
                        unset($images[$key]);
                    }
                }
                $idsVoted = array_merge($idsVoted, array_keys($images));
            }

            if ($imagesOption) {
                $idsVoted = array_merge($idsVoted, array_keys($imagesOption));
            }

            if ($images || $imagesOption) {
                $imagesDelete = Option::whereIn('id', array_unique($idsVoted))
                    ->pluck('image')
                    ->toArray();

                $imageNames = $this->createFileName($images);

                $this->updateImage($images, $imageNames, $imagesDelete);
            }


            foreach ($poll->options as $option) {
                $idOption = $option->id;

                $image = !isset($imageNames['optionImage'][$idOption])
                    ? null
                    : $imageNames['optionImage'][$idOption];

                $optionText = $textOptions[$idOption];

                if (in_array($option->id, $idsVoted) && !$image) {
                    $option->update(['image' => null]);
                }

                if ($option->name == $optionText) {
                    if (isset($image)) {
                        $option->update(['image' => $image]);
                    }
                } elseif (isset($image)) {
                    $option->update(['image' => $image, 'name' => $optionText]);
                } else {
                    $option->update(['name' => $optionText]);
                }
            }

            DB::commit();

            return true;
        } catch (Exception $ex) {
            DB::rollBack();

            return false;
        }
    }

    public function getSettingsPoll($idPoll)
    {
        if (!$idPoll) {
            return [];
        }

        $arrSetting = [];

        $poll = $this->model->find($idPoll)->load('settings');
        $settings = config('settings.setting');

        foreach ($settings as $keySetting) {
            $arrSetting[$keySetting]['isHave'] = false;
            $arrSetting[$keySetting]['value'] = null;
        }

        foreach ($poll->settings as $pollSetting) {
            $arrSetting[$pollSetting->key]['isHave'] = true;
            $arrSetting[$pollSetting->key]['value'] = $pollSetting->value;
        }

        return $arrSetting;
    }

    public function showOptionDate($poll)
    {
        if (!$poll || $poll->options->isEmpty()) {
            return false;
        }

        // Init data
        $data = [
            'months' => [],
            'days' => [],
            'hours' => [],
            'participants' => collect([]),
            'notHour' => false,
            'text' => [],
        ];

        // Sort By Date For Options
        $options = $poll->options->sortBy(function ($option) {
            return strtotime($option->name);
        });

        $countMonth = $countDay = $indexMonth = $indexDay = $countNotHour = $countText = 0;

        $multipleChoice = $poll->multiple == trans('polls.label.multiple_choice');

        $options->load('users.options', 'participants.options');

        foreach ($options as $option) {
            $option->users->each(function ($item) use ($option) {
                $option->participants->push($item);
            });

            $data['participants'] = $data['participants']->push($option->participants->map(function ($participant) use ($option) {
                $class = get_class($participant);

                return [
                    'id_participant' => $class === Participant::class ? $class . $participant->id : uniqid(time(), true),
                    'name' => $participant->name,
                    'email' => $participant->email,
                    'id' => $class === Participant::class
                        ? $participant->options->pluck('id')
                        : collect([])->push($option->id),
                    'created_at' => $participant->pivot->created_at,
                    'voter' => [
                        'id' => $participant->id,
                        'vote_id' => $participant->pivot->id,
                        'user_id' => $class === Participant::class ? $participant->user_id : null,
                    ],
                ];
            }));

            // Check option if is date
            if ($opt = validateDate($option->name)) {
                // Get Day Month Year Of Option
                $monthYear = $opt['monthYear'];
                $day = $opt['day'];
                $hour = $opt['hour'];

                // Check option all not hour
                if ($hour == config('settings.hour_default')) $countNotHour++;

                // Set Month Year
                if (!isset($data['months'][$indexMonth]['month'])
                    || $data['months'][$indexMonth]['month'] != $monthYear
                ) {
                    $countMonth = 0;
                    $indexMonth++;
                    $data['months'][$indexMonth]['month'] = $monthYear;
                }

                $countMonth++;
                $data['months'][$indexMonth]['count'] = $countMonth;

                // Set Day Weeks
                if (!isset($data['days'][$monthYear][$indexDay]['day'])
                    || $data['days'][$monthYear][$indexDay]['day'] != $day
                ) {
                    $countDay = 0;
                    $indexDay++;
                    $data['days'][$monthYear][$indexDay]['day'] = $day;
                }

                $countDay++;
                $data['days'][$monthYear][$indexDay]['count'] = $countDay;

                // Set Hour
                $data['hours'][] = [
                    'hour' => $hour,
                    'id' => $option->id,
                    'counter' => $option->countVotes(),
                ];

                continue;
            }

            // Check option if is text
            $data['text'][] = [
                'text' => $option->name,
                'id' => $option->id,
                'counter' => $option->countVotes(),
            ];
            $countText++;
        }

        $data['participants'] = $data['participants']->flatten(1)->unique('id_participant')->sortBy(function ($voter) {
            return $voter['created_at']->timestamp;
        });

        // Check all option have hour
        if ($countNotHour && $countNotHour == $options->count() - $countText) {
                $data['notHour'] = true;
        }

        // List Id option sort by
        $data['id'] = array_reduce(array_merge($data['hours'], $data['text']), function ($carry, $data) {
            $carry[$data['id']]= $data['counter'];

            return $carry;
        });

        return $data;
    }

    public function getSocketOption($poll)
    {
        $settingsPoll = $this->getSettingsPoll($poll->id);

        // Show result options
        $optionDates = $this->showOptionDate($poll);

        $config = config('settings.setting');

        $isLimit = false;
        if ($limitVoter = (int) $settingsPoll[$config['set_limit']]['value']) {
            $isLimit = $optionDates['participants']->count() >= $limitVoter;
        }

        $listVoter = $poll->options->reduce(function ($lookup, $item) {
            $lookup[$item->id] = $item->listVoter();

            return $lookup;
        });

        $isHaveImages = $poll->isImages();

        $isHideResult = $settingsPoll[config('settings.setting.hide_result')]['isHave'];

        $numberOfVote = $settingsPoll[config('settings.setting.number_of_vote')]['isHave'];

        // layout result option for voted
        $dataView['html'] = view(
            'user.poll.vote_details_layouts',
            compact('optionDates')
        )->render();

        foreach ($poll->options as $option) {
            $viewListVoters = view(
                '.user.poll.option_horizontal_list_voter',
                compact(
                    'option',
                    'settingsPoll',
                    'poll',
                    'isHaveImages',
                    'isLimit',
                    'listVoter',
                    'numberOfVote'
                )
            );

            $optionDetail[$option->id] = [
                'list_voters' => $viewListVoters->render(),
                'nameOption' => $option->name,
            ];
        }


        // layout horizontal options
        $dataView['horizontalOption'] = [
            'optionDetail' => $optionDetail,
            'isLimit' => $isLimit,
            'isClosed' => $poll->isClosed(),
            'isTimeOut' => $poll->isTimeOut(),
            'isOwner' => \Gate::allows('administer', $poll),
            'isHideResult' => $isHideResult,
        ];

        // layout vertical options
        $dataView['verticalOption'] = view(
            '.user.poll.option_vertical',
            compact('settingsPoll', 'poll', 'isHaveImages', 'isLimit')
        )->render();

        // layout timeline options
        $dataView['timelineOption'] = view(
            '.user.poll.option_timeline',
            compact('poll', 'isLimit', 'settingsPoll', 'optionDates')
        )->render();

        // Count voter that voted all option
        $dataView['count_participant'] = $optionDates['participants']->count();

        // Count voter that voted each option
        $dataView['result'] = $poll->countVotesWithOption();

        // poll id
        $dataView['poll_id'] = $poll->id;

        return $dataView;
    }

    public function getSocketChart($poll)
    {
        $isHaveImages = $poll->isImages();

        $options = $poll->options;

        //data for draw chart
        $totalVote = $options->reduce(function ($carry, $option) {
            return $carry + $option->countVotes();
        });

        $optionRateBarChart = $totalVote ? [] : null;

        $optionRateBarChart = $options->filter(function ($option) {
            return $option->countVotes();
        })->map(function ($option) use ($isHaveImages) {
            $countOption = $option->countVotes();

            return $isHaveImages
                ? ['<img src="' . $option->showImage() . '" class="image-option-poll">'
                    . '<span class="name-option-poll">' . $option->name . '</span>',$countOption]
                : ['<p>' . $option->name . '</p>', $countOption];
        })
        ->values()
        ->toJson();

        $optionRatePieChart = json_encode($this->getDataToDrawPieChart($poll, $isHaveImages));

        $chartNameData = json_encode($this->getNameOptionToDrawChart($poll, $isHaveImages));

        $fontSize = $this->getSizeChart($poll)['fontSize'];

        //get data result to sort number of vote
        $dataTableResult = $this->getDataTableResult($poll);

        // sort option and count vote by number of vote
        $dataTableResult = collect($dataTableResult)->sortByDesc(function ($data) {
            return $data['numberOfVote'];
        })
        ->values()
        ->toArray();

        // html result vote
        $dataChart['html_result_vote'] = view(
            'user.poll.result_vote_layouts',
            compact('dataTableResult', 'isHaveImages')
        )->render();

        $dataChart['html_pie_bar_manage_chart'] = view('user.poll.pie_bar_manage_chart_layouts')->render();

        // get pointer pie bar chart
        $dataChart['html_pie_bar_chart'] = view('user.poll.pie_bar_chart_layouts')->render();

        // get layout pie chart
        $dataChart['htmlPieChart'] = view(
            'user.poll.piechart_layouts',
            compact('optionRatePieChart', 'isHaveImages')
        )->render();

        // get layout bar chart
        $dataChart['htmlBarChart'] = view(
            'user.poll.barchart_layouts',
            compact('optionRateBarChart', 'chartNameData', 'fontSize')
        )->render();

        return $dataChart;
    }
}
