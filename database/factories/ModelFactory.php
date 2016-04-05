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

$factory->define(Wind\Models\UserModel::class, function ($faker) {
    $data = [
        'name' => $faker->name,
        'phone' => $faker->numberBetween($min = 10000000000, $max = 99999999999),
        'open_id' => generate_student_id(),
        'password' => '111111',
    ];
    return $data;
});
