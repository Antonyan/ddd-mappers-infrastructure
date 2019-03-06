<?php

namespace Infrastructure\Listeners;

use Exception;
use Infrastructure\Events\RequestEvent;
use Infrastructure\Models\ValidationRulesReader;
use Infrastructure\Models\Validator;

class RequestListener
{
    private const PIPE = '|';
    private const EQUAL_RULE = 'eq';

    /**
     * @param RequestEvent $event
     * @throws Exception
     */
    public function onRequest(RequestEvent $event)
    {
        $validationRulesReader = new ValidationRulesReader($event->getController(), $event->getMethodName());
        $validator = new Validator($validationRulesReader->rules());
        $validator->validate(
            array_merge(
                $event->getRequest()->request->all(),
                $event->getRequest()->attributes->all(),
                $event->getRequest()->query->all()
            )
        );

        $this->filterRequest($event, $validationRulesReader);

        $event->getRequest()->query->replace($this->extractConditions($event->getRequest()->query->all()));
    }

    /**
     * @param RequestEvent $event
     * @param $validationRulesReader
     */
    private function filterRequest(RequestEvent $event, $validationRulesReader): void
    {
        $validationFields = $validationRulesReader->validationFields();
        $request = $event->getRequest();

        $request->request->replace(array_intersect_key($request->request->all(), array_flip($validationFields)));
        $request->query->replace(array_intersect_key($request->query->all(), array_flip($validationFields)));
        $request->attributes->replace(array_intersect_key($request->attributes->all(), array_flip($validationFields)));
    }

    /**
     * @param $query
     * @return array
     */
    private function extractConditions($query): array
    {
        $data = [];

        foreach ($query as $field => $parameter) {
            if (strpos($parameter, self::PIPE)) {
                list($rule, $value) = explode(self::PIPE, $parameter);
                $data[trim($rule, "(")][trim($field)] = trim($value, ")");
                continue;
            }

            $data[self::EQUAL_RULE][$field] = trim($parameter);
        }
        
        return $data;
    }
}