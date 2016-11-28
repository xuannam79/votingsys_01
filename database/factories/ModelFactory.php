<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\Models\User::class, function (Faker\Generator $faker) {
    return [
        'email' => $faker->email,
        'name' => $faker->name,
        'password' => 'password',
        'chatwork_id' => $faker->name,
        'avatar' => 'default.jpg',
        'gender' => $faker->numberBetween(0, 2),
        'role' => 0,
        'token_verification' => '',
        'is_active' => 1,
    ];
});

$factory->define(App\Models\Poll::class, function (Faker\Generator $faker) {
    static $userIds;

    return [
        'user_id' => $faker->randomElement($userIds ?: $userIds = App\Models\User::pluck('id')->toArray()),
        'title' => $faker->text,
        'description' => $faker->paragraph,
        'location' => $faker->address,
        'status' => $faker->boolean,
        'multiple' => $faker->boolean,
    ];
});

$factory->define(App\Models\Option::class, function (Faker\Generator $faker) {
    static $pollIds;

    return [
        'poll_id' => $faker->randomElement($pollIds ?: $pollIds = App\Models\Poll::pluck('id')->toArray()),
        'name' => $faker->word,
        'image' => 'default-thumb.gif',
    ];
});

$factory->define(App\Models\Comment::class, function (Faker\Generator $faker) {
    static $pollIds;
    static $userIds;

    return [
        'poll_id' => $faker->randomElement($pollIds ?: $pollIds = App\Models\Poll::pluck('id')->toArray()),
        'user_id' => $faker->randomElement($userIds ?: $userIds = App\Models\User::pluck('id')->toArray()),
        'content' => $faker->paragraph,
        'name' => $faker->name,
    ];
});

$factory->define(App\Models\Setting::class, function (Faker\Generator $faker) {
    static $pollIds;

    return [
        'poll_id' => $faker->randomElement($pollIds ?: $pollIds = App\Models\Poll::pluck('id')->toArray()),
        'key' => $faker->numberBetween(1, 6),
        'value' => $faker->numberBetween(0, 50),
    ];
});
