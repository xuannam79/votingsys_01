<?php

namespace App\Repositories\Poll;

use App\Models\Activity;
use App\Models\Link;
use App\Models\Option;
use App\Models\Participant;
use App\Models\Poll;
use App\Models\Setting;
use Auth;
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

/*------------------------------------------------------------
 *                  [ADMIN] - POLL
 *------------------------------------------------------------*/

    /**
     *
     * Admin save a new poll
     *
     * @param $input
     *
     * @return string|\Symfony\Component\Translation\TranslatorInterface
     */
    public function store($input)
    {
        try {
            DB::beginTransaction();
            $now = Carbon::now();

            //insert poll information
            $pollId = Poll::insertGetId([
                'user_id' => auth()->user()->id,
                'title' => $input['title'],
                'description' => $input['description'],
                'multiple' => $input['type'],
            ]);

            //insert link of poll
            $linkVote = str_random(config('settings.length_poll.link'));
            $linkAdmin = str_random(config('settings.length_poll.link'));

            //link user
            if ($input[config('settings.input_setting.link')]) {
                $linkVote = (isset($input['link']) && $input['link'] ? $input['link'] : $linkVote);
            }

            Link::firstOrCreate([
                'poll_id' => $pollId,
                'token' => $linkVote,
                'link_admin' => config('settings.link_poll.vote'),
            ]);

            //link admin
            Link::firstOrCreate([
                'poll_id' => $pollId,
                'token' => $linkAdmin,
                'link_admin' => config('settings.link_poll.admin'),
            ]);

            //insert settting of poll
            $dataSetting = [];

            if ($input[config('settings.input_setting.email')]) {
                $dataSetting[] = [
                    'poll_id' => $pollId,
                    'key' => config('settings.setting.required_email'), //1: key required_mail
                    'value' => null,
                    'created_at' => $now,
                ];
            }

            if ($input[config('settings.input_setting.answer')]) {
                $dataSetting[] = [
                    'poll_id' => $pollId,
                    'key' => config('settings.setting.add_answer'), //2: add answer
                    'value' => null,
                    'created_at' => $now,
                ];
            }

            if ($input[config('settings.input_setting.result')]) {
                $dataSetting[] = [
                    'poll_id' => $pollId,
                    'key' => config('settings.setting.hide_result'), //3: hide result
                    'value' => null,
                    'created_at' => $now,
                ];
            }

            if ($input[config('settings.input_setting.link')]) {
                $dataSetting[] = [
                    'poll_id' => $pollId,
                    'key' => config('settings.setting.custom_link'), //4: custom link
                    'value' => (isset($input['link']) && $input['link']) ? $input['link'] : $linkVote,
                    'created_at' => $now,
                ];
            }

            if ($input[config('settings.input_setting.limit')]) {
                $dataSetting[] = [
                    'poll_id' => $pollId,
                    'key' => config('settings.setting.set_limit'), //5: set limit
                    'value' => (isset($input['limit']) && $input['limit']) ? $input['limit'] : null,
                    'created_at' => $now,
                ];
            }

            if ($input[config('settings.input_setting.password')]) {
                $dataSetting[] = [
                    'poll_id' => $pollId,
                    'key' => config('settings.setting.set_password'), //6: set password
                    'value' => (isset($input['password_poll']) && $input['password_poll'])
                        ? $input['password_poll'] : null,
                    'created_at' => $now,
                ];
            }

            if ($dataSetting) {
                Setting::insert($dataSetting);
            }

            //insert poll options
            $dataOption = [];

            if (is_array($input['option']) && $input['option']) {
                foreach ($input['option'] as $key => $option) {
                    $optionImage = array_get($input['optionImage'], $key) ? $input['optionImage'][$key] : null;
                    if ($option && $optionImage) {
                        $dataOption[] = [
                            'poll_id' => $pollId,
                            'name' => $option,
                            'image' => $optionImage->getClientOriginalName(),
                            'created_at' => $now,
                        ];
                    } elseif (! $option && $optionImage) {
                        $dataOption[] = [
                            'poll_id' => $pollId,
                            'name' => $option,
                            'image' => null,
                            'created_at' => $now,
                        ];
                    } elseif ($option && ! $optionImage) {
                        $dataOption[] = [
                            'poll_id' => $pollId,
                            'name' => null,
                            'image' => $optionImage->getClientOriginalName(),
                            'created_at' => $now,
                        ];
                    }
                }
            }

            if ($dataOption) {
                Option::insert($dataOption);
            }

            DB::commit();

            //upload image of option
            if (is_array($input['optionImage']) && $input['optionImage']) {
                foreach ($input['optionImage'] as $optionImage) {
                    if (! $optionImage) {
                        continue;
                    }

                    try {
                        $path = public_path() . config('settings.option.path_image');
                        $pathFileOption = '';

                        do {
                            //upload file
                            $fileOption =  uniqid(rand(), true) . '.' . $optionImage->getClientOriginalExtension();
                            $pathFileOption = $path . $fileOption;
                        } while (File::exists($pathFileOption));

                        $optionImage->move($path, $pathFileOption);
                    } catch (Exception $ex) {
                        throw new Exception(trans('polls.message.upload_image_fail'));
                    }
                }
            }

            //send mail
            $linkMail = url("/") . config('settings.email.link_vote') . $linkVote;
            $linkAdminPoll = url("/") . config('settings.email.link_vote') . $linkAdmin;
            $mailCreator = $input['email'];
            $participants = [];

            if ($input['invite'] == config('settings.participant.invite_people')) {

                //list participants
                $participants = array_filter($input['email_poll']);
            }

            //send mail participant
            if ($participants) {
                Mail::send('layouts.participant_mail', [
                    'link' => $linkMail,
                ], function ($message) use ($participants) {
                    $message->to($participants)->subject(trans('label.mail.subject'));
                });
            }

            //send mail admin
            Mail::send('layouts.creator_mail', [
                'linkAdmin' => $linkAdminPoll,
                'link' => $linkMail,
            ], function ($message) use ($mailCreator) {
                $message->to($mailCreator)->subject(trans('label.mail.subject'));
            });

            $message = trans('polls.message.create_success');
        } catch (Exception $ex) {
            DB::rollBack();
            $message = trans('polls.message.create_fail');
        }

        return $message;
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
        $config = config('settings.type');
        $trans = trans('polls.label');

        if ($isKey) {
            //return result type key: 0, 1
            return ($type == $trans['multiple_choice'] ? $config['multiple_choice']: $config['single_choice']);
        }

        //return result type text: multiple, single
        return ($type == $config['multiple_choice'] ? $trans['multiple_choice']: $trans['single_choice']);
    }

}
