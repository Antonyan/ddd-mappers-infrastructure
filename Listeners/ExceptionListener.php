<?php
namespace Infrastructure\Listeners;

use Infrastructure\Exceptions\HttpExceptionInterface;
use Infrastructure\Models\HttpExceptionFactory;
use Infrastructure\Services\ErrorHandlerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionListener implements EventSubscriberInterface
{
    protected $logger;
    protected $debug;
    protected $errorHandler;

    private const LOG_PRIORITY = 0;
    private const HANDLE_EXCEPTION_PRIORITY = -128;

    /**
     * ExceptionListener constructor.
     * @param $errorHandler
     * @param LoggerInterface|null $logger
     * @param bool $debug
     */
    public function __construct(ErrorHandlerInterface $errorHandler, LoggerInterface $logger = null, $debug = false)
    {
        $this->logger = $logger;
        $this->debug = $debug;
        $this->errorHandler = $errorHandler;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::EXCEPTION => array(
                array('logKernelException', self::LOG_PRIORITY),
                array('onKernelException', self::HANDLE_EXCEPTION_PRIORITY),
            ),
        );
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function logKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        $this->logException($exception, sprintf('Uncaught PHP Exception %s: "%s" at %s line %s', \get_class($exception), $exception->getMessage(), $exception->getFile(), $exception->getLine()));
    }

    /**
     * Logs an exception.
     *
     * @param \Exception $exception The \Exception instance
     * @param string     $message   The error message to log
     */
    protected function logException(\Exception $exception, $message)
    {
        if (null !== $this->logger) {
            if (!$exception instanceof HttpExceptionInterface || $exception->getStatusCode() >= 500) {
                $this->logger->critical($message, array('exception' => $exception));
            } else {
                $this->logger->error($message, array('exception' => $exception));
            }
        }
    }


    /**
     * @param GetResponseForExceptionEvent $event
     * @throws \ReflectionException
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        try {
            $response = $this->errorHandler->handle((new HttpExceptionFactory())->create($exception));
        } catch (\Exception $e) {
            $this->logException($e, sprintf('Exception thrown when handling an exception (%s: %s at %s line %s)', \get_class($e), $e->getMessage(), $e->getFile(), $e->getLine()));

            $this->throwDeepestHandlerExceptionWithAttachedOriginalException($exception, $e);
        }

        $event->setResponse($response);
    }

    /**
     * @param \Exception $originalException
     * @param \Exception $handlerFailException
     * @throws \ReflectionException
     */
    private function throwDeepestHandlerExceptionWithAttachedOriginalException(\Exception $originalException, \Exception $handlerFailException)
    {
        $wrapper = $handlerFailException;

        while ($prev = $wrapper->getPrevious()) {
            if ($originalException === $wrapper = $prev) {
                throw $handlerFailException;
            }
        }

        $prev = new \ReflectionProperty($wrapper instanceof \Exception ? \Exception::class : \Error::class, 'previous');
        $prev->setAccessible(true);
        $prev->setValue($wrapper, $originalException);

        throw $handlerFailException;
    }
}