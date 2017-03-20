<?php

namespace App\RepositoriesApi;

use App\Models\Poll;
use App\Models\Vote;
use App\RepositoriesApi\Contracts\PollRepositoryInterface;
use App\Mail\InviteParticipant;
use App\Mail\CreatePoll;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Models\Participant;
use App\Mail\CloseOrReOpenPoll;
use App\Mail\DeleteVotedPoll;
use Carbon\Carbon;
use App\Models\Option;
use Auth;
use File;
use Session;
use Intervention\Image\Facades\Image;

class PollRepositoryEloquent extends AbstractRepositoryEloquent implements PollRepositoryInterface
{
    public function __construct(Poll $model)
    {
        parent::__construct($model);
    }

    public function storePoll($input = [])
    {
        try {
            DB::beginTransaction();

            $poll = $this->addInfo($input);
            $link = $this->addLink($poll, $input);

            if (!$poll || !$this->addOption($poll, $input) || !$link) {
                DB::rollBack();

                return false;
            }

            $settings = $this->addSetting($poll, $input);

            $poll->load('settings', 'links', 'user');
            /*
             * Send mail participant
             */
            if ($input['member']) {
                $members = array_map('trim', explode(',', $input['member']));

                Mail::to($members)->queue(new InviteParticipant($poll));
            }

            /*
             * Send mail creator
             */
            Mail::to($poll->getEmailCreator())->queue(new CreatePoll($poll));

            DB::commit();


            return $poll->withoutAppends();
        } catch (Exception $e) {
            DB::rollBack();

            return false;
        }
    }

    private function addInfo($input)
    {
        try {
            $poll = new Poll;
            $userId = $this->getUserId($input['email']);

            $input['user_id'] = $userId;
            $input['name'] = $userId ? null : $input['name'];
            $input['email'] = $userId ? null : $input['email'];

            if ($poll->fill($input)->save()) {
                return $poll;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function addOption($poll, $input)
    {
        $options = $this->createImage($input);

        if (!$poll || !$options) {
            return false;
        }

        if ($poll->options()->createMany($options)) {
            return true;
        }

        return false;
    }

    private function createImage($input)
    {
        $optionText = $input['optionText'];
        $optionImage = $input['optionImage'];

        if (!$optionText) {
            return [];
        }

        try {
            $option = [];

            foreach ($optionText as $key => $text) {
                if ($text) {
                    $image = isset($optionImage[$key]) ? $optionImage[$key] : null;
                    $option[] = [
                        'name' => $text,
                        'image' => uploadImage($image, config('settings.option.path_image')),
                    ];
                }
            }

            return $option;
        } catch (Exception $e) {
            return [];
        }
    }

    public function addSetting($poll, $input)
    {
        try {
            if (!$poll) {
                return false;
            }

            if ($settings = $this->createSetting($input)) {
                $poll->settings()->delete();

                return $poll->settings()->createMany($settings);
            }
        } catch (Exception $e) {

        }

        return false;
    }

    private function createSetting($input)
    {
        $settings = $input['setting'];
        $value = $input['value'];
        $settingChilds = $input['setting_child'];
        $configRequired = config('settings.setting.required');

        $data = [];
        if ($settings) {
            foreach ($settings as $key => $setting) {
                // Set data for setting
                $keySetting = ($setting == $configRequired) ? $settingChilds[$configRequired] : $setting;
                $valueSetting = isset($value[$key]) ? $value[$key] : null;

                $data[] = [
                    'key' => $keySetting,
                    'value' => $valueSetting,
                ];
            }
        }

        return $data;
    }

    private function addLink($poll, $input)
    {
        $stCustomLink = config('settings.setting.custom_link');

        $linkPolls = [];
        for ($role = 0; $role < config('settings.link_limit'); $role++) {
            $link = str_random(config('settings.length_poll.link'));

            if ($role == config('settings.link_poll.vote')) {
                $link = isset($input['value'][$stCustomLink]) && $input['setting']
                    ? $input['value'][$stCustomLink]
                    : str_random(config('settings.length_poll.link'))
                ;
            }

            $linkPolls[] = [
                'token' => $link,
                'link_admin' => $role,
            ];
        }

        return $poll->links()->createMany($linkPolls);
    }

    public function editPoll($poll, $input)
    {
        DB::beginTransaction();
        try {
            $pollInfo = array_only($input, ['name', 'email', 'title', 'description', 'location', 'multiple', 'date_close']);

            if ($user = $poll->user) {
                $user->forceFill(['name' => $pollInfo['name'], 'email' => $pollInfo['email']]);
                $user->save();

                $pollInfo = array_only($input, ['title', 'description', 'location', 'multiple', 'date_close']);
            }

            // Save activity
            $this->createActivity($poll, config('settings.activity.edit_poll'));

            if ($poll->forceFill($pollInfo)->save()) {
                DB::commit();

                return true;
            }

            DB::rollBack();

            return false;
        } catch (Exception $e) {
            DB::rollback();

            return false;
        }
    }

    public function editOption($poll, $input)
    {
        try {
            $optionText = $input['optionText'];
            $optionImage = $input['optionImage'];

            if (!$optionText || !$poll) {
                return false;
            }

            $options = $poll->options;

            foreach ($optionText as $key => $text) {
                if ($text) {
                    $isOldOption = $options->contains('id', $key);

                    $option = $options->where('id', $key)->first();

                    $id =  $isOldOption ? $option->id : 0;

                    $image = isset($optionImage[$key])
                        ? $optionImage[$key]
                        : null
                    ;

                    $oldImage = $isOldOption && isset($optionImage[$key])
                        ? $option->image
                        : null
                    ;

                    $values = [
                        'name' => $text,
                        'image' => $isOldOption && is_null($image)
                            ? $option->image
                            : uploadImage($image, config('settings.option.path_image'), $oldImage),
                    ];

                    $poll->options()->updateOrCreate(['id' => $id], $values);
                }
            }

            //Save activity of poll
            $this->createActivity($poll, config('settings.activity.edit_poll'));

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    private function createActivity($poll, $type)
    {
        $id = isset($poll->user_id) ? $poll->user_id : null;

        $name = isset($poll->user)
            ? $poll->user->name . '(' . $poll->user->email . ')'
            : (isset($poll->name) && isset($poll->email) ? $poll->name . '(' . $poll->email . ')' : null)
        ;

        $activity = [
            'user_id' => $id,
            'type' => $type,
            'name' => $name,
        ];

        return $poll->activities()->create($activity);
    }

    public function getPollWithLinks($id)
    {
        return $poll = $this->model->with('links')->find($id);
    }

    public function getClosedPolls($userId)
    {
        if (empty($userId)) {
            return [];
        }

        $params = [
            'user_id' => $userId,
            'status' => config('settings.status.close'),
        ];

        $polls = $this->model->where($params)->orderBy('id', 'desc')->get();

        $polls = $polls->map(function ($poll) {
            return $poll->withoutAppends()->load('activities', 'links');
        });

        return $polls;
    }

    public function getSettingsPoll($poll)
    {
        $arrSetting = [];

        $settings = config('settings.setting');

        foreach ($settings as $keySetting) {
            $arrSetting[$keySetting]['status'] = false;
            $arrSetting[$keySetting]['value'] = null;
        }

        foreach ($poll->settings as $pollSetting) {
            $arrSetting[$pollSetting->key]['status'] = true;
            $arrSetting[$pollSetting->key]['value'] = $pollSetting->value;
        }

        return $arrSetting;
    }

    public function vote($poll, $input)
    {
        if (!$input['option']) {
            return false;
        }

        DB::beginTransaction();
        try {
            $input['user_id'] = $this->getUserId($input['email']);

            $idOption = array_values($input['option']);

            $user = $this->currentUser();

            if ($user && $user->name == $input['name'] && $user->email == $input['email']) {
                $user->options()->attach($idOption);
                DB::commit();

                return true;
            }

            $participant = new Participant;

            if (!$participant->fill($input)->save()) {
                return false;
            }

            // Add Voter that is voting
            $participant->options()->attach($idOption);
            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();

            return false;
        }
    }

    public function getPollsOfUser($userId)
    {
        if (empty($userId)) {
            return [];
        }

        $polls = $this->model->where([
            'user_id' => $userId
        ])->orderBy('id', 'desc')->get();
        $polls = $polls->map(function ($poll) {
            return $poll->withoutAppends()->load('activities', 'links');
        });

        return $polls;
    }

    public function getParticipatedPolls($currentUser)
    {
        try {
            $listPollIds = [];
            $votes = Vote::where('user_id', $currentUser->id)->with('option.poll')->get();

            foreach ($votes as $vote) {
                if ($vote->option->poll) {
                    $listPollIds[] = $vote->option->poll->id;
                }
            }

            $participantPolls = $this->model->whereIn('id', array_unique($listPollIds))->orderBy('id', 'desc')->get();

            foreach ($currentUser->participantVotes as $participantVote) {
                $participantPolls->push($participantVote->option->poll);
            }

            $participantPolls = $participantPolls->map(function ($poll) {
                return $poll->withoutAppends()->load('activities', 'links');
            });

            return $participantPolls->unique();
        } catch (Exception $e) {
            return false;
        }
    }

    public function closeOrOpen($poll)
    {
        DB::beginTransaction();
        try {
            $poll->withoutAppends();

            $poll->status = (int) !$poll->status;

            // Create Activity
            if (!$poll->save()) {
                return false;
            }

            // Save activity
            $activity = $poll->status ? config('settings.activity.reopen_poll') : config('settings.activity.close_poll');
            $this->createActivity($poll, $activity);

            DB::commit();

            /**
             * Send mail to poll creator
             */
            $email = $poll->user_id ? $poll->user->email : $poll->email;
            Mail::to($email)->queue(new CloseOrReOpenPoll($poll));

            return true;
        } catch (Exception $e) {
            DB::rollBack();

            return false;
        }
    }

    public function resetVoted($poll)
    {
        if ($poll->options->isEmpty()) {
            return false;
        }

        DB::beginTransaction();
        try {
            foreach ($poll->options as $option) {
                $option->users()->detach();
                $option->participants()->detach();
            }

            DB::commit();

            $this->createActivity($poll, config('settings.activity.all_participants_deleted'));

            //Send mail to admin when user delete all voted of options
            $email = $poll->user_id ? $poll->user->email : $poll->email;
            Mail::to($email)->queue(new DeleteVotedPoll($poll->getAdminLink()));

            return true;
        } catch (Exception $e) {
            DB::rollBack();

            return false;
        }
    }

    public function comment($poll, $input)
    {
        DB::beginTransaction();
        try {
            $input['user_id'] = $this->getUserId();

            $comment = $poll->comments()->create($input);

            DB::commit();

            return $comment;
        } catch (Exception $e) {
            DB::rollBack();

            return false;
        }
    }

    public function resultsVoted($poll)
    {
        try {
            $data['results'] = collect([]);

            $poll->load('options.participantVotes', 'options.votes');

            foreach ($poll->options as $option) {
                $data['results']->push([
                    'name' => $option->name,
                    'image' => $option->showImage(),
                    'voters' => $option->countVotes(),
                ]);
            }

            return $data;
        } catch (Exception $e) {
            return false;
        }
    }

    public function store($input)
    {
        DB::beginTransaction();
        try {
            $poll = $this->addInfo($input);

            if (!$poll || !($this->addDuplicateOption($input, $poll->id) && $this->addSetting($poll, $input))) {
                DB::rollback();

                return false;
            }

            $links = $this->addLink($poll, $input);

            if (!$links) {
                DB::rollback();

                return false;
            }

            $poll = Poll::with('user')->find($poll->id);

            // send mail participant
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

            // send mail creator
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
        DB::beginTransaction();
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
                    ];
                } elseif ($oldImage && array_key_exists($key, $oldImage)) {
                    $dataInsert[] = [
                        'poll_id' => $pollId,
                        'name' => $value,
                        'image' => $oldImage[$key],
                    ];
                } else {
                    $dataInsert[] = [
                        'poll_id' => $pollId,
                        'name' => $value,
                        'image' => ($nameImage && array_key_exists($key, $nameImage['optionImage'])) ?
                            $nameImage['optionImage'][$key] : null,
                    ];
                }
            }

            if ($dataInsert) {
                Option::insert($dataInsert);
                $this->updateImage($optionOldImage, $nameOldImage);
                $this->updateImage($optionImage, $nameImage);
            }
            DB::commit();

            return true;
        } catch (Exception $ex) {
            DB::rollback();

            return false;
        }
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
            // delete old image
            if (is_array($oldImages) && $oldImages) {
                foreach ($oldImages as $image) {
                    $path = public_path() . config('settings.option.path_image') . $image;
                    if (File::exists($path)) {
                        File::delete($path);
                    }
                }
            }

            // upload new image
            if ($images) {
                foreach ($images as $key => $image) {
                    $img = Image::make($image);
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

    public function sendMailAgain($poll)
    {
        try {
            $poll->load('links', 'settings', 'user');

            $email = $poll->user_id ? $poll->user->email : $poll->email;

            // Get link of poll
            $links = $poll->links->reduce(function ($lookup, $link) {
                $lookup[$link->link_admin] = $link->token;

                return $lookup;
            }, []);

            // Get settings
            $settings = $poll->settings->reduce(function ($settingLookup, $setting) {
                $settingLookup[$setting->key] = $setting->value;

                return $settingLookup;
            }, []);

            $data = [
                'userName' => $poll->user_id ? $poll->user->name : $poll->name,
                'title' => $poll->title,
                'type' => $poll->multiple,
                'location' => $poll->location,
                'description' => $poll->description,
                'closeDate' => $poll->date_close,
                'createdAt' => $poll->created_at,
                'linkVote' => action('LinkController@show', ['token' => $links[config('settings.link_poll.vote')]]),
                'linkAdmin' => action('LinkController@show', ['token' => $links[config('settings.link_poll.admin')]]),
                'password' => isset($settings[config('settings.setting.set_password')])
                    ? $settings[config('settings.setting.set_password')] : null,
            ];

            $creatorView = config('settings.view.poll_mail');

            $subject = trans('label.mail.create_poll.subject');

            $this->sendEmail($email, $creatorView, $data, $subject);

            return $poll->withoutAppends();
        } catch (Exception $e) {
            return false;
        }
    }
}
