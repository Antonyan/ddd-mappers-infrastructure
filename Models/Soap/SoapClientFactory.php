<?php
namespace Infrastructure\Models\Soap;

use Infrastructure\Exceptions\InfrastructureException;

class SoapClientFactory
{
    /**
     * @param array $config
     * @return Client
     * @throws InfrastructureException
     */
    public function create(array $config) : Client
    {
        $className = $this->soapClientClass($config);
        return new $className(
            $config['options'] ?? [],
            $config['wsdl'] ?? null
        );
    }

    /**
     * @param array $config
     * @return mixed
     * @throws InfrastructureException
     */
    private function soapClientClass(array $config)
    {
        if(empty($config['soapClientClass'])) {
            throw new InfrastructureException('Please configure soap module, set Soap Client Class.');
        }

        return $config['soapClientClass'];
    }
}