<?php declare(strict_types=1);

namespace Infrastructure\Services;

use Exception;
use Infrastructure\Exceptions\InternalException;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class EventCommandsExecutor
{
    /**
     * @var ContainerBuilder
     */
    private $eventCommands;

    /**
     * EventCommandsExecutor constructor.
     * @param ContainerBuilder $eventCommands
     */
    public function __construct(ContainerBuilder $eventCommands)
    {
        $this->eventCommands = $eventCommands;
    }

    /**
     * @param array $commands
     * @param array $event
     * @throws Exception
     * @throws InternalException
     */
    public function execute(array $commands, array $event): void
    {
        foreach ($commands as $command) {
            $this->command($command)->execute($event);
        }
    }

    /**
     * @param string $commandName
     * @return ExecutableEventCommand
     * @throws InternalException
     * @throws Exception
     */
    private function command(string $commandName): ExecutableEventCommand
    {
        if (!$this->eventCommands->has($commandName)){
            throw new InternalException('There isn\'t command ' . $commandName . ' for event execution.');
        }

        return $this->eventCommands->get($commandName);
    }
}