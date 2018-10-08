<?php
namespace Infrastructure\Models\Soap;

use Infrastructure\Exceptions\InfrastructureException;

class SoapClientFactory
{
    /**
     * @param array $soapConfig
     * @return SoapClient
     * @throws InfrastructureException
     */
    public function create(array $soapConfig) : SoapClient
    {
        $className = $this->soapClientClass($soapConfig);
        return new $className(
            $soapConfig['options'] ?? [],
            $soapConfig['wsdl'] ?? null
        );
    }

    /**
     * @param array $soapConfig
     * @return mixed
     * @throws InfrastructureException
     */
    private function soapClientClass(array $soapConfig)
    {
        if(empty($soapConfig['soapClientClass'])) {
            throw new InfrastructureException('Please configure soap module, set Soap Client Class.');
        }

        return $soapConfig['soapClientClass'];
    }
}