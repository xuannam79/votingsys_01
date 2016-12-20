<?php

use App\Models\User;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LoginTest extends TestCase
{
    public function testAdminLoginSuccess()
    {
        $this->visit('/login')
            ->type('admin@gmail.com', 'email')
            ->type('password', 'password')
            ->press(trans('label.login'))
            ->seePageIs('/admin/user');
    }

    public function testUserLoginSuccess()
    {
        $user = factory(User::class)->create();
        $this->visit('/login')
            ->type($user->email, 'email')
            ->type('password', 'password')
            ->press(trans('label.login'))
            ->seePageIs('/')
            ->see(trans('user.login_successfully'));
    }

    public function testUserLoginFail()
    {
        $faker = Faker\Factory::create();
        $this->visit('/login')
            ->type($faker->email, 'email')
            ->type($faker->password, 'password')
            ->press(trans('label.login'))
            ->seePageIs('/login')
            ->see(trans('user.login_fail'));
    }

    public function testLoginEgnoreEmailField()
    {
        $faker = Faker\Factory::create();
        $this->visit('/login')
            ->type('', 'email')
            ->type($faker->password, 'password')
            ->press(trans('label.login'))
            ->seePageIs('/login');
    }

    public function testLoginIgnorePasswordField()
    {
        $faker = Faker\Factory::create();
        $this->visit('/login')
            ->type($faker->email, 'email')
            ->type('', 'password')
            ->press(trans('label.login'))
            ->seePageIs('/login');
    }

    public function testLoginIncorrectField()
    {
        $faker = Faker\Factory::create();
        $this->visit('/login')
            ->type($faker->email, 'email')
            ->type($faker->password, 'password')
            ->press(trans('label.login'))
            ->seePageIs('/login');
    }

    public function testLoginInvalidEmail()
    {
        $this->visit('/login')
            ->type(str_random(10), 'email')
            ->type('', 'password')
            ->press(trans('label.login'))
            ->seePageIs('/login');
    }
}
