<?php

namespace Infrastructure\Models\Http\Response;


class RawResponse extends AbstractResponseDecorator
{
    /**
     * @return array
     */
    public function getParsedBody(): array
    {
        return [$this->getBody()->getContents()];
    }
}