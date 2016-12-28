<?php

use App\Models\User;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EditProfileTest extends TestCase
{
    public function testEditProfileIgnoreEmail()
    {
        $user = factory(User::class)->create();
        $this->visit('/login')
            ->type($user->email, 'email')
            ->type('password', 'password')
            ->press(trans('label.login'))
            ->visit('/user/profile')
            ->type('', 'email')
            ->press(trans('label.edit'))
            ->seePageIs('/user/profile');
    }

    public function testEditProfileIgnoreEmailAndName()
    {
        $user = factory(User::class)->create();
        $this->visit('/login')
            ->type($user->email, 'email')
            ->type('password', 'password')
            ->press(trans('label.login'))
            ->visit('/user/profile')
            ->type('', 'name')
            ->type('', 'email')
            ->press(trans('label.edit'))
            ->seePageIs('/user/profile');
    }

    public function testEditProfileWithEmailInvalid()
    {
        $user = factory(User::class)->create();
        $faker = Faker\Factory::create();
        $this->visit('/login')
            ->type($user->email, 'email')
            ->type('password', 'password')
            ->press(trans('label.login'))
            ->visit('/user/profile')
            ->type($faker->name, 'email')
            ->press(trans('label.edit'))
            ->seePageIs('/user/profile');
    }

    public function testEditProfileIgnoreName()
    {
        $user = factory(User::class)->create();
        $this->visit('/login')
            ->type($user->email, 'email')
            ->type('password', 'password')
            ->press(trans('label.login'))
            ->visit('/user/profile')
            ->type('', 'name')
            ->press(trans('label.edit'))
            ->seePageIs('/user/profile');
    }

    public function testConfirmPasswordIncorrect()
    {
        $user = factory(User::class)->create();
        $faker = Faker\Factory::create();
        $this->visit('/login')
            ->type($user->email, 'email')
            ->type('password', 'password')
            ->press(trans('label.login'))
            ->visit('/user/profile')
            ->type($faker->password, 'password')
            ->type($faker->password, 'password_confirmation')
            ->press(trans('label.edit'))
            ->seePageIs('/user/profile');
    }

    public function testEditProfileSuccess()
    {
        $user = factory(User::class)->create();
        $faker = Faker\Factory::create();
        $this->visit('/login')
            ->type($user->email, 'email')
            ->type('password', 'password')
            ->press(trans('label.login'))
            ->visit('/user/profile')
            ->type($faker->name, 'name')
            ->press(trans('label.edit'))
            ->see(trans('user.update_profile_successfully'))
            ->seePageIs('/');
    }
}
