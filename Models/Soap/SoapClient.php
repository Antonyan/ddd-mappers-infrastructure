<?php
namespace Infrastructure\Models\Soap;

class SoapClient extends \SoapClient
{
    /**
     * @param string $function_name
     * @param array $arguments
     * @return mixed
     * @throws SoapFaultException
     */
    public function __call($function_name, $arguments)
    {
        return $this->__soapCall($function_name, $arguments);
    }

    /**
     * @param string $function_name
     * @param array $arguments
     * @param null $options
     * @param null $input_headers
     * @param null $output_headers
     * @return mixed
     * @throws SoapFaultException
     */
    public function __soapCall($function_name, $arguments, $options = NULL, $input_headers = NULL, &$output_headers = NULL)
    {
        try {
            return parent::__soapCall($function_name, $arguments, $options, $input_headers,$output_headers);
        } catch (\SoapFault $soapFault) {
            throw new SoapFaultException($soapFault->getMessage());
        }
    }
}