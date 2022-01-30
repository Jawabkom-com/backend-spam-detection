<?php

namespace Jawabkom\Backend\Module\Spam\Detection\InputValidator;

use Jawabkom\Backend\Module\Spam\Detection\Contract\InputFilter\IInputFilter;
use Jawabkom\Backend\Module\Spam\Detection\Contract\InputValidator\IInputValidator;
use Jawabkom\Backend\Module\Spam\Detection\Exception\RequiredInputsException;
use Jawabkom\Backend\Module\Spam\Detection\Exception\RequiredPhoneException;
use Jawabkom\Backend\Module\Spam\Detection\Library\Phone;

class AbusePhoneReportInputsValidator implements IInputValidator
{
    public function validate(array $inputs): bool
    {
        throw new RequiredInputsException('reporter_id, phone, abuse_type');
    }
}