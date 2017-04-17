<?php

use App\Models\Poll;
use App\Models\User;
use App\Models\Option;

class ApiEditPollTest extends TestCase
{
    /*
     * unit test poll not exst
     *
     * @param int idPoll
     *
     * @expect status code 404
     * @expect error true
     * */
    public function testPollNotExist()
    {
        $this->post('api/v1/poll/update/' . random_int(1000000000, 2000000000));
        $this->assertResponseStatus(API_RESPONSE_CODE_NOT_FOUND);
        $this->seeJson([
            'error' => true,
            'messages' => [
                trans('polls.message.not_found_polls')
            ],
        ]);
    }

    /*
    * unit test name more than max
    *
    * @param string name: 101 char
    * @param string email
    * @param string title
    * @param string description
    * @param string multiple
    *
    * @expect status code 422
    * @expect error true
    * */
    public function testNameMoreThanMax()
    {
        $poll = factory(Poll::class)->create();
        $this->post('api/v1/poll/update/' . $poll->id, [
            'name' => str_random(101),
            'email' => str_random(10) . '@gmail.com',
            'title' => str_random(100),
            'description' => str_random(100),
            'multiple' => config('settings.type_poll.single_choice'),
            'type_edit' => config('settings.btn_edit_poll.save_info'),
        ]);
        $this->seeJson([
            'status' => API_RESPONSE_CODE_UNPROCESSABLE,
            'error' => true,
            'messages' => [
                trans('polls.validation')['name']['max'],
            ],
        ]);
    }

    /*
    * unit test title more than max
    *
    * @param string name
    * @param string email
    * @param string title: 256 char
    * @param string description
    * @param string multiple
    *
    * @expect status code 422
    * @expect error true
    * */
    public function testTitleMoreThanMax()
    {
        $poll = factory(Poll::class)->create();
        $this->post('/api/v1/poll/update/' . $poll->id, [
            'name' => str_random(10),
            'email' => str_random(10) . '@gmail.com',
            'title' => str_random(256),
            'description' => str_random(100),
            'multiple' => config('settings.type_poll.single_choice'),
            'type_edit' => config('settings.btn_edit_poll.save_info'),
        ]);
        $this->seeJson([
            'status' => API_RESPONSE_CODE_UNPROCESSABLE,
            'error' => true,
            'messages' => [
                trans('polls.validation')['title']['max'],
            ]
        ]);
    }

    /*
    * unit test not multiple
    *
    * @param string name
    * @param string email
    * @param string title
    * @param string description
    *
    * @expect status code 302
    * */
    public function testNotMultiple()
    {
        $poll = factory(Poll::class)->create();
        $this->post('/api/v1/poll/update/' . $poll->id, [
            'name' => str_random(10),
            'email' => str_random(10) . '@gmail.com',
            'title' => str_random(100),
            'description' => str_random(100),
            'type_edit' => config('settings.btn_edit_poll.save_info'),
            'type_edit' => config('settings.btn_edit_poll.save_info'),
        ]);
        $this->seeJson([
            'status' => API_RESPONSE_CODE_UNPROCESSABLE,
            'error' => true,
            'messages' => [
                trans('polls.validation')['type']['required'],
            ]
        ]);
    }

    /*
     * unit test edit poll not user
     *
     * @param string name
     * @param string email
     * @param string title
     * @param string description
     * @param string location
     * @param boolean multiple
     * @param date date_close
     * @param int type_edit
     *
     * @expect status code 200
     * @expect error false
     * */
    public function testEditPollNotUser()
    {
        $poll = factory(Poll::class)->create([
           'user_id' => random_int(1000000000, 2000000000),
        ]);
        $this->post('/api/v1/poll/update/' . $poll->id, [
            'name' => str_random(10),
            'email' => str_random(10) . '@gmail.com',
            'title' => str_random(20),
            'description' => str_random(100),
            'location' => str_random(10),
            'multiple' => config('settings.type_poll.single_choice'),
            'date_close' => date('d-m-Y H:i', strtotime('tomorrow')),
            'type_edit' => config('settings.btn_edit_poll.save_info'),
        ]);
        $this->assertResponseStatus(API_RESPONSE_CODE_OK);
        $this->seeJson([
            'error' => false,
            'messages' => [
                trans('polls.message.update_poll_info_success'),
            ]
        ]);
    }

    /*
     * unit test edit poll has user
     *
     * @param string name
     * @param string email
     * @param string title
     * @param string description
     * @param string location
     * @param boolean multiple
     * @param date date_close
     * @param int type_edit
     *
     * @expect status code 200
     * @expect error false
     * */
    public function testEditPollHasUser()
    {
        $user = factory(User::class)->create();
        $poll = factory(Poll::class)->create([
            'user_id' => $user->id,
        ]);
        $this->post('/api/v1/poll/update/' . $poll->id, [
            'name' => str_random(10),
            'email' => str_random(10) . '@gmail.com',
            'title' => str_random(20),
            'description' => str_random(100),
            'location' => str_random(10),
            'multiple' => config('settings.type_poll.single_choice'),
            'date_close' => date('d-m-Y H:i', strtotime('tomorrow')),
            'type_edit' => config('settings.btn_edit_poll.save_info'),
        ]);
        $this->assertResponseStatus(API_RESPONSE_CODE_OK);
        $this->seeJson([
            'error' => false,
            'messages' => [
                trans('polls.message.update_poll_info_success'),
            ]
        ]);
    }

    /*
     * unit test param option null
     *
     * @param int type_edit
     *
     * @expect status code 422
     * @expect error true
     * */
    public function testParamOptionNull()
    {
        $poll = factory(Poll::class)->create();
        $this->post('/api/v1/poll/update/' . $poll->id, [
            'type_edit' => config('settings.btn_edit_poll.save_option'),
        ]);
        $this->assertResponseStatus(API_RESPONSE_CODE_UNPROCESSABLE);
        $this->seeJson([
            'error' => true,
            'messages' => [
                trans('polls.message.update_option_fail'),
            ]
        ]);
    }

    /*
    * unit test option of poll null
    *
    * @param array optionText
    * @param array optionImage
    * @param int type_edit
    *
    * @expect status code 422
    * @expect error true
    * */
    public function testOptionOfPollNull()
    {
        $poll = factory(Poll::class)->create();
        $optionText = [
            str_random(10),
            str_random(10),
        ];
        $optionImage = [
            'http://placeimg.com/640/480/any',
            'http://placeimg.com/640/480/any',
        ];
        $this->post('/api/v1/poll/update/' . $poll->id, [
            'optionText' => $optionText,
            'optionImage' => $optionImage,
            'type_edit' => config('settings.btn_edit_poll.save_option'),
        ]);
        $this->assertResponseStatus(API_RESPONSE_CODE_UNPROCESSABLE);
        $this->seeJson([
            'error' => true,
            'messages' => [
                trans('polls.message.update_option_fail'),
            ]
        ]);
    }

    /*
    * unit test update option of poll
    *
    * @param array optionText
    * @param array optionImage
    * @param int type_edit
    *
    * @expect status code 200
    * @expect error true
    * */
    public function testUpdateOptionOfPoll()
    {
        $poll = factory(Poll::class)->create();
        factory(Option::class)->create([
            'poll_id' => $poll->id,
        ]);
        factory(Option::class)->create([
            'poll_id' => $poll->id,
        ]);
        $optionText = [
            str_random(10),
            str_random(10),
        ];
        $optionImage = [
            'http://placeimg.com/640/480/any',
            'http://placeimg.com/640/480/any',
        ];
        $this->post('/api/v1/poll/update/' . $poll->id, [
            'optionText' => $optionText,
            'optionImage' => $optionImage,
            'type_edit' => config('settings.btn_edit_poll.save_option'),
        ]);
        $this->assertResponseStatus(API_RESPONSE_CODE_OK);
        $this->seeJson([
            'error' => false,
            'messages' => [
                trans('polls.message.update_option_success'),
            ]
        ]);
    }

    /*
    * unit test update setting of poll
    *
    * @param array setting
    * @param array value
    * @param array setting_child
    * @param array optionImage
    * @param int type_edit
    *
    * @expect status code 422
    * @expect error false
    * */
    public function testUpdateSettingOfPoll()
    {
        $poll = factory(Poll::class)->create();
        factory(\App\Models\Setting::class)->create([
            'poll_id' => $poll->id,
        ]);
        $setting = [
            0 => config('settings.setting.required'),
            4 => config('settings.setting.set_limit'),
            5 => config('settings.setting.set_password'),
        ];
        $setting_child = [
            0 => 7,
        ];
        $value = [
            4 => 5,
            5 => str_random(10),
        ];
        $this->post('/api/v1/poll/update/' . $poll->id, [
            'setting' => $setting,
            'value' => $value,
            'setting_child' => $setting_child,
            'type_edit' => config('settings.btn_edit_poll.save_setting'),
        ]);
        $this->assertResponseStatus(API_RESPONSE_CODE_OK);
        $this->seeJson([
            'error' => false,
            'messages' => [
                trans('polls.message.update_setting_success'),
            ],
        ]);
    }
}
