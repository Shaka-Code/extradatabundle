<?php

namespace ExtraDataBundle\Validator;

use Symfony\Component\Validator\Context\ExecutionContextInterface;

class JSONValidator
{

    /**
     * @param string $value
     * @param ExecutionContextInterface $context
     * @param array $payload
     */
    public static function validate($value, ExecutionContextInterface $context, $payload)
    {
        if (isset($payload['field']) && $value) {
            json_decode($value);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $context->buildViolation('error.json_format_invalid')
                        ->atPath($payload['field'])
                        ->addViolation();
            }
        }
    }

}
