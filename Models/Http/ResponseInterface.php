<?php

namespace Infrastructure\Models\Http;


use \Psr\Http\Message\ResponseInterface as PsrResponseInterface;

interface ResponseInterface extends PsrResponseInterface
{
    public const CONTENT_TYPE_JSON =  'application/json;charset=utf-8';
    public const CONTENT_TYPE_XML = 'text/xml';

    /**
     * @return mixed
     */
    public function getParsedBody();
}