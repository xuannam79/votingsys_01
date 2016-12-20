<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::truncate();
        factory(User::class, 5)->create();

        //create Admin account
        factory(User::class)->create([
            'email' => 'admin@gmail.com',
            'name' => 'Admin',
            'password' => 'password',
            'avatar' => 'default.jpg',
            'chatwork_id' => '12345678',
            'gender' => 1,
            'role' => 1,
            'token_verification' => '',
            'is_active' => 1,
        ]);
    }
}
