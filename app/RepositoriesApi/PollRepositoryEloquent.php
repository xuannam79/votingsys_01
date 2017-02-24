<?php

namespace App\RepositoriesApi;

use App\Models\Poll;
use App\RepositoriesApi\Contracts\PollRepositoryInterface;
use App\Mail\InviteParticipant;
use App\Mail\CreatePoll;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Exception;

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

            $poll->load('settings', 'links', 'user');

            $settings = $this->addSetting($poll, $input);

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
            return false;
        }
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
}
