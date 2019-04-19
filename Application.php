<?php

namespace Infrastructure;

use Exception;
use Infrastructure\Events\RequestEvent;
use Infrastructure\Exceptions\InternalException;
use Infrastructure\Exceptions\ResourceNotFoundException as InternalResourceNotFoundException;

use Infrastructure\Models\ContainerBuilder;
use Infrastructure\Models\ApplicationExceptionInfo;
use Infrastructure\Services\ApplicationLogService;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Routing\RouteCollection;

class Application extends HttpKernel
{
    public const ENV_PROD = 'prod';
    public const ENV_APPLICATION_NAME = 'APPLICATION_NAME';

    public const EVENT_BEFORE_DISPATCH_REQUEST = 'request';

    /**
     * @var UrlMatcherInterface
     */
    private $matcher;

    /**
     * @var ControllerResolver
     */
    private $controllerResolver;

    /**
     * @var ArgumentResolver
     */
    private $argumentResolver;

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @var ApplicationLogService
     */
    private $logService;

    /**
     * @var string
     */
    private $env;

    /**
     * Application constructor.
     * @param RouteCollection $routes
     * @param ContainerBuilder $appContainer
     * @throws Exception
     */
    public function __construct(RouteCollection $routes, ContainerBuilder $appContainer) {
        $this->env = getenv('ENV') ?: self::ENV_PROD;
        /** @var ContainerBuilder $container */
        $container = include __DIR__.'/config/appContainer.php';
        $container->merge($appContainer);
        $container->compile();
        $this->matcher = $container->get('matcher');
        $this->controllerResolver = $container->get('controllerResolver');
        $this->argumentResolver = $container->get('argumentResolver');
        $this->eventDispatcher = $container->get('dispatcher');
        $this->logService = $container->get('logService');

        parent::__construct(
            $this->eventDispatcher,
            $this->controllerResolver,
            $container->get('requestStack'),
            $this->argumentResolver
        );
    }

    /**
     * @param Request $request
     * @param int $type
     * @param bool $catch Is symfony param and used for sub request which is internal, and
     *      if exception occurred it signalize about infrastructure error and should not be caught
     * @return Response
     * @throws Exception
     */
    public function handle(Request $request,
                           $type = HttpKernelInterface::MASTER_REQUEST,
                           $catch = true)
    {
        $this->matcher->getContext()->fromRequest($request);

        try {
            if($type == HttpKernelInterface::MASTER_REQUEST) {
                $request->attributes->add($this->matcher->match($request->getPathInfo()));
            }

            $controller = $this->controllerResolver->getController($request);
            $arguments = $this->argumentResolver->getArguments($request, $controller);

            if($type == HttpKernelInterface::MASTER_REQUEST) {
                $this->eventDispatcher->dispatch(self::EVENT_BEFORE_DISPATCH_REQUEST, new RequestEvent($request, $controller[0], $controller[1]));
            }

            $response = call_user_func_array($controller, $arguments);
        } catch (MethodNotAllowedException $methodNotAllowedException) {
            $response = $this->handleException(
                $request,
                $type,
                new InternalException('Method Not Allowed!', Response::HTTP_METHOD_NOT_ALLOWED),
                $catch
            );
        } catch (ResourceNotFoundException $exception) {
            $response = $this->handleException(
                $request,
                $type,
                new InternalResourceNotFoundException('Resource not found!'),
                $catch
            );
        } catch (Exception $exception) {
            $response = $this->handleException($request, $type, $exception, $catch);

            if ($response->getStatusCode() === Response::HTTP_INTERNAL_SERVER_ERROR) {
                $this->getApplicationLogService()->getLogger()->critical(
                    'Exception: ' . $exception->getMessage(),
                    (new ApplicationExceptionInfo($request, $controller[0], $controller[1]))->toArray()
                );
            }

        } catch (\Error $error) {
            $this->getApplicationLogService()->getLogger()->critical(
                'Error: ' . $error->getMessage(),
                (new ApplicationExceptionInfo($request, $controller[0], $controller[1]))->toArray()
            );
            $response = $this->handleException($request, $type, new Exception($error->getMessage(), $error->getCode(), $error), $catch);
        }

        return $response;
    }

    /**
     * @param Request $request
     * @param int $type
     * @param Exception $exception
     * @param bool $catch
     * @return Response
     * @throws Exception
     */
    private function handleException(Request $request, int $type, \Exception $exception, bool $catch): Response
    {
        if ($catch === false) {
            throw $exception;
        }

        $event = new GetResponseForExceptionEvent($this, $request, $type, $exception);
        $this->dispatcher->dispatch(KernelEvents::EXCEPTION, $event);

        // a listener might have replaced the exception
        $exception = $event->getException();

        if (! $event->hasResponse()) {
            $this->throwUncaughtInfrastructureException($exception);
        }

        return $event->getResponse();
    }

    /**
     * @param Exception $exception
     * @throws Exception
     */
    private function throwUncaughtInfrastructureException(Exception $exception)
    {
        $this->getApplicationLogService()->getLogger()->critical(
            'Error: ' . $exception->getMessage(),
            $exception->getTrace()
        );

        if ($this->env === self::ENV_PROD) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'Internal server error');
        }

        throw $exception;
    }

    /**
     * @return ApplicationLogService
     */
    private function getApplicationLogService(): ApplicationLogService
    {
        return $this->logService;
    }
}