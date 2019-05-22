<?php

use Infrastructure\Application;

use Infrastructure\Factories\LoggerFactory;
use Infrastructure\Models\ContainerBuilder;
use Infrastructure\Services\ApplicationLogService;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\EventListener\ResponseListener;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Infrastructure\Listeners\ExceptionListener;

$containerBuilder = new ContainerBuilder();

$containerBuilder->addCompilerPass(new \Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass('dispatcher'));

$containerBuilder->register('context', RequestContext::class);
$containerBuilder->register('matcher', UrlMatcher::class)
    ->setArguments([$routes, new Reference('context')])
;
$containerBuilder->register('requestStack', RequestStack::class);
$containerBuilder->register('controllerResolver', ControllerResolver::class);
$containerBuilder->register('argumentResolver', ArgumentResolver::class);

$containerBuilder->register('listener.router', RouterListener::class)
    ->setArguments([new Reference('matcher'), new Reference('requestStack')])
;
$containerBuilder->register('listener.response', ResponseListener::class)
    ->setArguments(['UTF-8'])
;
$containerBuilder->register('application.error.handler', \Infrastructure\Services\ErrorHandlerHandler::class);

$containerBuilder->register('listener.exception', ExceptionListener::class)
    ->setArguments([
        new Reference('application.error.handler')
    ])
;

$containerBuilder->register('listener.request', \Infrastructure\Listeners\RequestListener::class);
$containerBuilder->register('listener.auth', \Infrastructure\Listeners\AuthListener::class);

$containerBuilder->register('dispatcher', EventDispatcher::class)
    ->addMethodCall('addSubscriber', [new Reference('listener.router')])
    ->addMethodCall('addSubscriber', [new Reference('listener.response')])
    ->addMethodCall('addSubscriber', [new Reference('listener.exception')])
    ->addMethodCall('addListener', [Application::EVENT_BEFORE_DISPATCH_REQUEST, [new Reference('listener.request'), 'onRequest']])
    ->addMethodCall('addListener', [Application::EVENT_BEFORE_DISPATCH_REQUEST, [new Reference('listener.auth'), 'onRequest']])
;

$containerBuilder->register('application', Application::class)
    ->setArguments([
        new Reference('controllerResolver'),
        new Reference('requestStack'),
        new Reference('argumentResolver'),
        new Reference('dispatcher'),
    ])
;

$containerBuilder->register('logService', ApplicationLogService::class)
    ->addArgument(new Reference('LoggerFactory'));


return $containerBuilder;