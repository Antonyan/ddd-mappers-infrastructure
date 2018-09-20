<?php

namespace Infrastructure\Mappers;


use Infrastructure\Models\Http\ResponseInterface;

interface HttpMapperErrorHandler
{
    public function handle(ResponseInterface $response);
}