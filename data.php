<?php
// ставки пользователей, которыми надо заполнить таблицу
$bets = [
    ['name' => 'Иван', 'price' => 11500, 'ts' => strtotime('-' . rand(1, 50) .' minute')],
    ['name' => 'Константин', 'price' => 11000, 'ts' => strtotime('-' . rand(1, 18) .' hour')],
    ['name' => 'Евгений', 'price' => 10500, 'ts' => strtotime('-' . rand(25, 50) .' hour')],
    ['name' => 'Семён', 'price' => 10000, 'ts' => strtotime('last week')]
];

//$categories = ["Доски и лыжи", "Крепления", "Ботинки", "Одежда", "Инструменты", "Разное"];

//$ads = [
//    [
//        'name' => '2014 Rossignol District Snowboard',
//        'category' => 'Доски и лыжи',
//        'price' => '10999',
//        'imgUrl' => 'img/lot-1.jpg'
//    ], [
//        'name' => 'DC Ply Mens 2016/2017 Snowboard',
//        'category' => 'Доски и лыжи',
//        'price' => '15999',
//        'imgUrl' => 'img/lot-2.jpg'
//    ], [
//        'name' => 'Крепления Union Contact Pro 2015 года размер L/XL',
//        'category' => 'Крепления',
//        'price' => '8000',
//        'imgUrl' => 'img/lot-3.jpg'
//    ], [
//        'name' => 'Ботинки для сноуборда DC Mutiny Charocal',
//        'category' => 'Ботинки',
//        'price' => '10999',
//        'imgUrl' => 'img/lot-4.jpg'
//    ], [
//        'name' => 'Куртка для сноуборда DC Mutiny Charocal',
//        'category' => 'Одежда',
//        'price' => '7500.33',
//        'imgUrl' => 'img/lot-5.jpg'
//    ], [
//        'name' => 'Маска Oakley Canopy',
//        'category' => 'Разное',
//        'price' => '10999.93',
//        'imgUrl' => 'img/lot-6.jpg'
//    ]];