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
    ->addArgument([
        LoggerFactory::FILE => new FileLogFactory(LOG_PATH, new StreamProvider()),
        LoggerFactory::CLOUD_WATCH => new CloudWatchLogFactory(
            getenv('APPLICATION_NAME') ?: '',
            getenv('ENV')?: '',
            new CloudWatchProvider()
        ),
        LoggerFactory::SYSLOG => new ErrorLogFactory(new ErrorLogProvider()),
        LoggerFactory::ERROR_LOG => new SysLogFactory(new SysLogProvider(
            (getenv('APPLICATION_NAME') ?: '') . '-'.(getenv('ENV')?: '')
        ))
    ]);