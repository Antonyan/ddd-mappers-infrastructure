# ddd-mappers-infrastructure
Infrastructure layer for [DDD project](https://github.com/Antonyan/ddd-mappers-project) based on Data Mapper as Data source pattern [Data Mapper](https://martinfowler.com/eaaCatalog/dataMapper.html)
<br/>

## General
In case if you use this layer with [DDD project](https://github.com/Antonyan/ddd-mappers-project) all functionality will be plugged automatically. 
An entry point is an Application class. Which uses Symfony HttpKernel as an engine.
#### Main flow
1. An application gets a controller (Service of the presentation layer) and method from a request.
1. Dispatch event for request preprocessing: filtering, validation etc.
1. Call controller.
#### Dependency management
The architecture of the current application implies that main business logic will place in services. 
Each module and context will have representative service.
Service extends BaseService which get a container (Dependency Injection) and config from "config" folder at the level above. On top of that container for each Service is automatically merged with infrastructure container.
#### Infrastructure container
Infrastructure container includes **db** connection, **MySqlClient**, **RequestFactory**, **HttpClient**.

## DB interaction
#### Connection
If you're using MySQL as DB you should specify  DDD_RBD_NAME, DDD_RBD_USER, DDD_RBD_PASSWORD, DDD_RBD_HOST, DDD_RBD_DRIVER (pdo_mysql) as env variables or just in .env file of the [DDD project] (https://github.com/Antonyan/ddd-mappers-project)
#### DbMapper
For [Rapid application development (RAD)](https://en.wikipedia.org/wiki/Rapid_application_development) we were created such abstraction as DbMapper.
If you need CRUD implementation only it'll be supported out of the box. All that you need is to specify table and fields mapping in Module config.
For create and update you should specify identifiers names. 
Config example:
```
'DeliveryDbTranslator' => [
        'table' => 'deliveries',
        'columns' => [
			'id' => 'deliveries.id',
			'deliveryCostId' => 'deliveries.deliveryCostId',
			'orderId' => 'deliveries.orderId',
			'status' => 'deliveries.status',
			'deliveryTime' => 'deliveries.deliveryTime',
			'locationId' => 'deliveries.locationId',
			'deliveryPhone' => 'deliveries.deliveryPhone',
			'contactPerson' => 'deliveries.contactPerson',
			'notice' => 'deliveries.notice',
        ],
        'create' => 'id',
        'update' => ['id'],
    ],
```
## Http interaction
#### HttpMapper
If the resource that you need you should get from another service (by HTTP(s)) you can use HTTP mapper which interface is similar to DbMapper.
Config example:
```
return [
    'httpConfig' => [
        'availableUrls' => [
            'get' => getenv('SOME_MICROSERVICE_BASE_URL').'/users/:id',
        ]
    ],
];
```

## Logging
For logging purpose we're using [Monolog](https://github.com/Seldaek/monolog), but of course, we encapsulated it to rid of dependencies.
We support logging to the file and to the [CloudWatch](https://aws.amazon.com/cloudwatch/).
To use logging you should create Service (example):
```
class SomeLogger extends LogService
{
    /**
     * @return array
     */
    protected function loggersMap(): array
    {
        return [
            self::LOG_TO_FILE => function () {return (new FileLoggingRegistry())->logger(LOG_PATH . 'someLog.log', 'channelName');},
            self::LOG_TO_CLOUD_WATCH => function () {return (new CloudWatchLoggingRegistry())->logger('groupName', 'streamName', 'channelName');},
        ];
    }

}
```
On top of that, you should specify env variable with log destination LOGGING_TYPE = file or 
LOGGING_TYPE = cloudWatch

## Customize your services
- ```application.error.handler```is reserved service to override to handle api fail response on exception
