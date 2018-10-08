<?php
namespace Infrastructure\Mappers;

use Infrastructure\Models\Soap\SoapClient;

abstract class SoapMapper
{
    private $soapClient;

    public function __construct(SoapClient $soapClient)
    {
        $this->soapClient = $soapClient;
    }


    protected function soapClient()
    {
        return $this->soapClient;
    }
}