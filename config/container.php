<?php

use Infrastructure\Factories\LoggerFactory;
use Infrastructure\Services\MySQLClient;

$containerBuilder->register('db', \Infrastructure\Services\DbConnection::class)
    ->addArgument($containerBuilder->get('config')->database);

$containerBuilder->register('MySqlClient', MySQLClient::class)
    ->addArgument($containerBuilder->get('config')->database);

$containerBuilder->register('RequestFactory', \Infrastructure\Models\Http\GuzzleRequestFactory::class);
$containerBuilder->register('HttpClient', \Infrastructure\Models\Http\HttpClient::class);


$containerBuilder->register('LoggerFactory', LoggerFactory::class)
    ->addArgument(LOG_PATH, getenv('APPLICATION_NAME') ?: '');