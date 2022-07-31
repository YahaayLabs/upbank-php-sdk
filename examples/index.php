<?php

require_once  __DIR__ . '/../vendor/autoload.php'; // change path as needed

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$apiKey = $_ENV["UPBANK_APIKEY"];
$client = new YahaayLabs\UpBank\Client($apiKey);

$accounts = $client->accounts->all()->data();
array_walk( $accounts, function($item) {
    echo "{$item->id} => {$item->attributes->displayName} <br/>";
});

$categories = $client->categories->all()->data();
array_walk( $categories, function ($item) {
    echo "{$item->id} => {$item->id} <br/>";
});
$tags = $client->tags->all()->data();
array_walk( $tags, function ($item) {
    echo "{$item->id} => {$item->id} <br/>";
});

$transactions = $client->transactions->all()->data();
array_walk( $client->transactions->all()->data(), function ($item) {
    echo "{$item->id} => {$item->attributes->description} <br/>";
});

$record = $client->transactions->get('498d9fbe-4fca-4663-b870-7c193f59cc89')->getRaw();
var_dump($record);