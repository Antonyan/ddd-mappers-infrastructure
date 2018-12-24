<?php

namespace Infrastructure\Models;

use Infrastructure\Services\BaseService;
use Symfony\Component\HttpFoundation\Request;

class ApplicationExceptionInfo implements ArraySerializable
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var BaseService
     */
    private $controller;

    /**
     * @var string
     */
    private $methodName;

    /**
     * ApplicationExceptionInfo constructor.
     * @param Request $request
     * @param BaseService $controller
     * @param string $mthodName
     */
    public function __construct(Request $request, BaseService $controller, string $mthodName)
    {
        $this->request = $request;
        $this->controller = $controller;
        $this->methodName = $mthodName;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'url' => $this->request->getUri(),
            'headers-roles' => $this->request->headers->get('roles'),
            'headers-userId' => $this->request->headers->get('userId'),
            'request' => $this->request->request->all(),
            'query' => $this->request->query->all(),
            'attributes' => $this->request->attributes->all(),
            'controller' => $this->controller,
            'method' => $this->methodName,
        ];
    }
}