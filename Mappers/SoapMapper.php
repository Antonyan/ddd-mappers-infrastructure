<?php
namespace Infrastructure\Mappers;

use Infrastructure\Models\Soap\Client;

abstract class SoapMapper
{
    private $soapClient;

    public function __construct(Client $soapClient)
    {
        $this->soapClient = $soapClient;
    }


    protected function soapClient()
    {
        return $this->soapClient;
    }
}