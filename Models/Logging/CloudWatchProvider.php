<?php

namespace Infrastructure\Models\Logging;

use Aws\CloudWatchLogs\CloudWatchLogsClient;
use Infrastructure\Exceptions\InfrastructureException;
use InvalidArgumentException;
use Maxbanton\Cwh\Handler\CloudWatch as ExternalCloudWatch;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\AbstractProcessingHandler;

class CloudWatchProvider
{
    private const ONE_MONTH = 30;

    private $requiredAWSParams = [
        'AWS_REGION', 'AWS_ACCESS_KEY_ID', 'AWS_SECRET_ACCESS_KEY'
    ];

    /**
     * @param $groupName
     * @param $streamName
     * @param int $retentionDays
     * @return ExternalCloudWatch
     * @throws InfrastructureException
     */
    public function handler($groupName, $streamName, $retentionDays = self::ONE_MONTH) : AbstractProcessingHandler
    {
        try {
            return (new ExternalCloudWatch(new CloudWatchLogsClient($this->awsParams()), $groupName, $streamName, $retentionDays))
                ->setFormatter(new LineFormatter(null, null, false, true));
        } catch (InvalidArgumentException $exception){
            throw new InfrastructureException('Can\'t initialize CloudWatch ' . $exception->getMessage());
        }
    }

    /**
     * @return array
     * @throws InfrastructureException
     */
    private function awsParams() : array
    {
        foreach ($this->requiredAWSParams as $param) {
            if (!getenv($param)) {
                throw new InfrastructureException('For AWS logging parameter ' . $param . 'didn\'t specify.');
            }
        }

        return [
            'region' => getenv('AWS_REGION'),
            'version' => 'latest',
            'credentials' => [
                'key' => getenv('AWS_ACCESS_KEY_ID'),
                'secret' => getenv('AWS_SECRET_ACCESS_KEY'),
            ]
        ];
    }
}