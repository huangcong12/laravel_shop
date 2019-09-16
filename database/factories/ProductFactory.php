<?php

/** @var Factory $factory */

use App\Product;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Product::class, function (Faker $faker) {
    $image = $faker->randomElement([
        'https://public-10006067.file.myqcloud.com//upload/imagelist/thumb/f88560b64240304f6b9ee64d6d8f8cab.jpeg',
        'https://public-10006067.file.myqcloud.com//upload/imagelist/thumb/d34f70754e1a2ec5012db93d2eab0271.jpeg',
        'https://public-10006067.file.myqcloud.com//upload/imagelist/thumb/112b8fc649de50b6534fad7bd7aaf5c9.jpeg',
        'https://public-10006067.file.myqcloud.com//upload/imagelist/thumb/b2225f2f9e018ed202b7822efcf945b4.jpeg',
        'https://public-10006067.file.myqcloud.com//upload/imagelist/thumb/aabee1f6cd840283d1bc47863e5b6fcd.jpeg',
        'https://public-10006067.file.myqcloud.com//upload/imagelist/thumb/f4223d52a6b3384dfe083c908cd0d409.jpeg',
        'https://public-10006067.file.myqcloud.com//upload/imagelist/thumb/de845eb4447fb47b40d870c47093bf96.jpeg',
        'https://public-10006067.file.myqcloud.com//upload/imagelist/thumb/d04e297d63c78fa009234f725230e951.jpeg',
        'https://public-10006067.file.myqcloud.com//upload/imagelist/thumb/ba5aa668c728e6e43c09968e8a07faf8.jpeg'
    ]);
    return [
        'title' => $faker->word,
        'description' => $faker->sentence,
        'image' => $image,
        'on_sale' => true,
        'rating' => $faker->randomNumber(2),
        'sold_count' => $faker->randomNumber(3),
        'review_count' => $faker->randomNumber(3),
        'price' => 0,
    ];
});
