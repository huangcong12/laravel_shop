<?php

/** @var Factory $factory */

use App\ProductSku;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(ProductSku::class, function (Faker $faker) {
    return [
        'title' => $faker->word,
        'description' => $faker->sentence,
        'price' => $faker->randomNumber(4),
        'stock' => $faker->randomNumber(5),
    ];
});
