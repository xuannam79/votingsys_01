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
                        } while(File::exists($pathFileOption));

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
}
