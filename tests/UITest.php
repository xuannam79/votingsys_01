<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UITest extends TestCase
{
    public function testUI()
    {
        $this->visit('/')->click(trans('label.login'))->seePageIs('/login');
        $this->visit('/')->click(trans('label.register'))->seePageIs('/register');
        $this->visit('/')->click(trans('label.home'))->seePageIs('/');
        $this->visit('/')->click(trans('label.tutorial'))->seePageIs('/tutorial');
        $this->visit('/login')->click(trans('label.forgot_password'))->seePageIs('/password/reset');
        $this->visit('/')->click(trans('label.tutorial'))->seePageIs('/tutorial');
    }
}
