<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RegisterTest extends TestCase
{
    public function testRegisterSuccess()
    {
        $faker = Faker\Factory::create();
        $password = $faker->password;
        $this->visit('/register')
            ->type($faker->name, 'name')
            ->type($faker->email, 'email')
            ->type(0, 'gender')
            ->type($password, 'password')
            ->type($password, 'password_confirmation')
            ->press(trans('label.register'))
            ->seePageIs('/login')
            ->see(trans('user.register_account'));
    }

    public function testRegisterFail()
    {
        $this->visit('/register')
            ->type('', 'name')
            ->type('', 'email')
            ->type(0, 'gender')
            ->type('', 'password')
            ->type('', 'password_confirmation')
            ->press(trans('label.register'))
            ->seePageIs('/register');
    }

    public function testRegisterIncorrectField()
    {
        $faker = Faker\Factory::create();
        $this->visit('/register')
            ->type($faker->name, 'name')
            ->type($faker->email, 'email')
            ->press(trans('label.register'))
            ->seePageIs('/register');
    }
}
