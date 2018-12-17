<?php

namespace Infrastructure\Models\Http\Response;


class EmptyResponse extends AbstractResponseDecorator
{
    /**
     * @return array
     */
    public function getParsedBody(): array
    {
        return [];
    }
}