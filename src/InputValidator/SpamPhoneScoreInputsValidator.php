<?php

namespace Jawabkom\Backend\Module\Spam\Detection\InputValidator;

use Jawabkom\Backend\Module\Spam\Detection\Contract\InputValidator\IInputValidator;
use Jawabkom\Backend\Module\Spam\Detection\Exception\RequiredInputsException;
use Jawabkom\Backend\Module\Spam\Detection\Exception\RequiredPhoneException;

class SpamPhoneScoreInputsValidator implements IInputValidator
{
    /**
     * @throws RequiredInputsException
     * @throws RequiredPhoneException
     */
    public function validate(array $inputs): bool
    {
        if(!isset($inputs['phone'])) {
            throw new RequiredPhoneException();
        }
        throw new RequiredInputsException();

    }
}