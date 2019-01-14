<?php

use Faker\Generator as Faker;

$factory->define(App\Person::class, function (Faker $faker) {
    return [
        'firstName' => $faker->firstName,
        'lastName' => $faker->lastName,
        'birthdate' => $faker->date($format = 'Y-m-d', $max = 'now')
    ];
});
