<?php

namespace Infrastructure\Listeners;

use App\Services\Auth;
use Exception;
use Infrastructure\Events\RequestEvent;
use Infrastructure\Models\ValidationRulesReader;
use Infrastructure\Models\Validator;

class AuthListener
{
    /**
     * @param RequestEvent $event
     * @throws Exception
     */
    public function onRequest(RequestEvent $event)
    {
        if (!class_exists('App\Services\Auth')){
            return;
        }

        (new Auth())->checkPermissions($event->getRequest(), $event->getController(), $event->getMethodName());
    }
}