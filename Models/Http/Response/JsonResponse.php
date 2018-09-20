<?php

namespace Infrastructure\Models\Http\Response;


class JsonResponse extends AbstractResponseDecorator
{
    /**
     * @return array
     */
    public function getParsedBody(): array
    {
        return json_decode($this->getBody()->getContents());
    }
}