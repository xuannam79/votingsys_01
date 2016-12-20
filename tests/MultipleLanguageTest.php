<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MultipleLanguageTest extends TestCase
{
    public function testChoiseLanguageInHomePage()
    {
        $this->visit('/')
            ->select(config('settings.langguage.en'), 'lang')
            ->seePageIs('/');
    }

    public function testChoiseLanguageInLoginPage()
    {
        $this->visit('/login')
            ->select(config('settings.langguage.vi'), 'lang')
            ->seePageIs('/login');
    }

    public function testChoiseLanguageInRegisterPage()
    {
        $this->visit('/register')
            ->select(config('settings.langguage.ja'), 'lang')
            ->seePageIs('/register');
    }

    public function testChoiseLanguageInResetPasswordPage()
    {
        $this->visit('/password/reset')
            ->select(config('settings.langguage.en'), 'lang')
            ->seePageIs('/password/reset');
    }
}
