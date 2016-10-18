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
        'avatar' => $faker->imageUrl(100, 100),
        'gender' => $faker->numberBetween(0, 2),
        'role' => 0,
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
        'name' => $faker->text,
        'image' => $faker->imageUrl(100, 100),
    ];
});

$factory->define(App\Models\Participant::class, function (Faker\Generator $faker) {
    static $userIds;

    return [
        'name' => $faker->name,
        'user_id' => $faker->randomElement($userIds ?: $userIds = App\Models\User::pluck('id')->toArray()),
        'email' => $faker->email,
        'ip_address' => $faker->ipv4,
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

$factory->define(App\Models\Activity::class, function (Faker\Generator $faker) {
    static $pollIds;
    static $userIds;

    return [
        'poll_id' => $faker->randomElement($pollIds ?: $pollIds = App\Models\Poll::pluck('id')->toArray()),
        'user_id' => $faker->randomElement($userIds ?: $userIds = App\Models\User::pluck('id')->toArray()),
        'type' => $faker->numberBetween(0, 3),
    ];
});

$factory->define(App\Models\Setting::class, function (Faker\Generator $faker) {
    static $pollIds;

    return [
        'poll_id' => $faker->randomElement($pollIds ?: $pollIds = App\Models\Poll::pluck('id')->toArray()),
        'key' => $faker->numberBetween(0, 3),
        'value' => $faker->numberBetween(0, 50),
    ];
});

$factory->define(App\Models\Vote::class, function (Faker\Generator $faker) {
    static $optionIds;
    static $userIds;

    return [
        'option_id' => $faker->randomElement($optionIds ?: $optionIds = App\Models\Option::pluck('id')->toArray()),
        'user_id' => $faker->randomElement($userIds ?: $userIds = App\Models\User::pluck('id')->toArray()),
    ];
});

$factory->define(App\Models\ParticipantVote::class, function (Faker\Generator $faker) {
    static $optionIds;
    static $participantIds;

    return [
        'option_id' => $faker->randomElement($optionIds ?: $optionIds = App\Models\Option::pluck('id')->toArray()),
        'participant_id' => $faker->randomElement($participantIds ?: $participantIds = App\Models\Participant::pluck('id')->toArray()),
    ];
});
