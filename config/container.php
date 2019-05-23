<?php

use Infrastructure\Factories\CloudWatchLogFactory;
use Infrastructure\Factories\ErrorLogFactory;
use Infrastructure\Factories\FileLogFactory;
use Infrastructure\Factories\LoggerFactory;
use Infrastructure\Factories\SysLogFactory;
use Infrastructure\Models\Logging\CloudWatchProvider;
use Infrastructure\Models\Logging\ErrorLogProvider;
use Infrastructure\Models\Logging\StreamProvider;
use Infrastructure\Models\Logging\SysLogProvider;
use Infrastructure\Services\MySQLClient;

$containerBuilder->register('db', \Infrastructure\Services\DbConnection::class)
    ->addArgument($containerBuilder->get('config')->database);

$containerBuilder->register('MySqlClient', MySQLClient::class)
    ->addArgument($containerBuilder->get('config')->database);

$containerBuilder->register('RequestFactory', \Infrastructure\Models\Http\GuzzleRequestFactory::class);
$containerBuilder->register('HttpClient', \Infrastructure\Models\Http\HttpClient::class);


$containerBuilder->register('LoggerFactory', LoggerFactory::class)
    ->addArgument(new FileLogFactory(LOG_PATH, new StreamProvider()))
    ->addArgument(
        new CloudWatchLogFactory(
            getenv('APPLICATION_NAME') ?: '',
            getenv('ENV')?: '',
            new CloudWatchProvider()
        )
    )
    ->addArgument(new ErrorLogFactory(new ErrorLogProvider()))
    ->addArgument(new SysLogFactory(new SysLogProvider(
        (getenv('APPLICATION_NAME') ?: '') . '-'.(getenv('ENV')?: '')
    )));