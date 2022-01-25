<?php

namespace Jawabkom\Backend\Module\Spam\Detection\InputValidator;

use Jawabkom\Backend\Module\Spam\Detection\Contract\InputValidator\IInputValidator;
use Jawabkom\Backend\Module\Spam\Detection\Exception\RequiredInputsException;

class SpamPhoneScoreInputsValidator implements IInputValidator
{

    public function validate(array $inputs): bool
    {
        throw new RequiredInputsException();
    }
}