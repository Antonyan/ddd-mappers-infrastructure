<?php

use Infrastructure\Factories\CloudWatchLogFactory;
use Infrastructure\Factories\FileLogFactory;
use Infrastructure\Factories\LoggerFactory;
use Infrastructure\Services\MySQLClient;

$containerBuilder->register('db', \Infrastructure\Services\DbConnection::class)
    ->addArgument($containerBuilder->get('config')->database);

$containerBuilder->register('MySqlClient', MySQLClient::class)
    ->addArgument($containerBuilder->get('config')->database);

$containerBuilder->register('RequestFactory', \Infrastructure\Models\Http\GuzzleRequestFactory::class);
$containerBuilder->register('HttpClient', \Infrastructure\Models\Http\HttpClient::class);


$containerBuilder->register('LoggerFactory', LoggerFactory::class)
    ->addArgument(new FileLogFactory(LOG_PATH))
    ->addArgument(
        new CloudWatchLogFactory(
            getenv('APPLICATION_NAME') ?: '',
            getenv('ENV')?: ''
        )
    );