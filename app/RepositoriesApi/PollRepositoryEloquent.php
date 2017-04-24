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

        if ($options = $poll->options()->createMany($options)) {
            return collect($options);
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
        $options = $poll->options;

        $wrongOption = collect($input)
            ->flatten(1)
            ->contains(function ($item) use ($options) {
                if (isset($item['id'])) {
                    return !$options->contains('id', $item['id']);
                }
            });

        if (!$input['option'] || !$poll || $wrongOption) {
            return false;
        }

        try {
            DB::beginTransaction();

            $create = [];
            $updated = [];

            foreach ($input['option'] as $option) {
                $isId = isset($option['id']) ? true : false;

                $realOption = $isId ? $options->where('id', $option['id'])->first() : null;

                if (isset($option['image'])) {
                    $oldImage = $realOption ? $realOption->image : null;
                    $option['image'] = uploadImage($option['image'], config('settings.option.path_image'), $oldImage);
                }

                if ($isId) {
                    $poll->options()->whereId($option['id'])->update($option);
                    $updated[] = $option['id'];
                } else {
                    $create[] = new Option($option);
                }
            }

            $options->reject(function ($option) use ($updated) {
                return in_array($option->id, $updated);
            })->each(function ($option) {
                // Delete Image
                if ($option->image) {
                    deleteImage(config('settings.option.path_image'), $option->image);
                }

                // Delete Partipants
                $option->participants()->delete();
                $option->participants()->detach();

                // Delete User
                $option->users()->detach();
            });

            $poll->options()->whereNotIn('id', $updated)->delete();

            if (count($create)) {
                $poll->options()->saveMany($create);
            }

            //Save activity of poll
            $this->createActivity($poll, config('settings.activity.edit_poll'));

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();

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
        if (!$input['option'] && !$input['optionText']) {
            return false;
        }

        DB::beginTransaction();
        try {
            $input['user_id'] = $this->getUserId($input['email']);

            $idOption = array_values((array) $input['option']);

            if (!empty($input['optionText'])) {
                $idOption = array_merge(
                    $idOption,
                    $this->addOption($poll, $input)->pluck('id')->toArray()
                );
            }

            $user = $this->currentUser();

            if ($user && $user->name == $input['name'] && $user->email == $input['email'] && !$poll->withoutAppends()->multiple) {
                $user->options()->attach($idOption);
                DB::commit();

                return $user;
            }

            $participant = new Participant;

            if (!$participant->fill($input)->save()) {
                return false;
            }

            // Add Voter that is voting
            $participant->options()->attach($idOption);
            DB::commit();

            return $participant;
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
            'user_id' => $userId,
            'status' => config('settings.status.open'),
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
                'poll' => $poll,
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

    public function getResultDetail($poll)
    {
        $results = $poll->options->map(function ($option) {
            // Push users to participants
            $option->users->each(function ($item) use ($option) {
                $option->participants->push($item);
            });

            return $option->participants->map(function ($participant) use ($option) {
                $class = get_class($participant);

                $getResult = function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'image' => $item->url_image,
                    ];
                };

                $votes = [];

                if ($class === Participant::class) {
                    $votes = $participant->options->map(function ($item) use ($getResult) {
                        return call_user_func($getResult, $item);
                    });
                } else {
                    $votes = call_user_func($getResult, $option);
                }

                return [
                    'id' => $class === Participant::class ? $class . $participant->id : uniqid(time(), true),
                    'created_at' => $participant->created_at,
                    'name' => $participant->name,
                    'email' => $participant->email,
                    'votes' => $votes,
                ];
            });
        })->flatMap(function ($flatten) {
            return $flatten;
        })->unique('id')->sortBy(function ($option) {
            return $option['created_at']->timestamp;
        })->map(function ($result) {
            unset($result['id'], $result['created_at']);

            return $result;
        })->values();

        return [
            'results' => $results,
        ];
    }
}
