<?php

namespace Infrastructure\Models;

use Infrastructure\Exceptions\ValidationException;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validation;

class Validator
{
    /**
     * @var array
     */
    private $rules;

    /**
     * Validator constructor.
     * @param array $rules
     */
    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    /**
     * @param array $dataForValidation
     * @throws ValidationException
     */
    public function validate(array $dataForValidation) : void
    {
        if (!$this->rules){
            return;
        }

        $validator = Validation::createValidator();
        $errorsMap = new StringMap();

        foreach ((new ValidationRulesTranslator())->translate($this->rules) as $item) {
            if (!array_key_exists($item->getName(), $dataForValidation)){
                $dataForValidation[$item->getName()] = null;
            }

            /** @var ConstraintViolation $error */
            $errors = $validator->validate($dataForValidation[$item->getName()], $item->getConstraints());
            foreach ($errors as $error) {
                $errorsMap->mergeIfExist($item->getName(), $error->getMessage());
            }
        }

        if (\count($errorsMap)){
            throw new ValidationException('Invalid request parameters', $errorsMap);
        }
    }
}
