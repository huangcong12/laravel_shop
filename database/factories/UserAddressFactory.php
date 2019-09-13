<?php

/** @var Factory $factory */

use App\UserAddress;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(UserAddress::class, function (Faker $faker) {
    $addresses = [
        ['北京市', '市辖区', '东城区'],
        ['广西', '南宁市', '兴宁区'],
        ['广东省', '广州市', '天河区'],
        ['江苏省', '苏州市', '相城区'],
        ['广东省', '深圳市', '福田区'],
    ];
    $address = $faker->randomElement($addresses);

    return [
        'province' => $address[0],
        'city' => $address[1],
        'district' => $address[2],
        'address' => sprintf('第%d街道第%d号', $faker->randomNumber(2), $faker->randomNumber()),
        'zip' => $faker->postcode,
        'contact_name' => $faker->name,
        'contact_phone' => $faker->phoneNumber,
    ];
});
