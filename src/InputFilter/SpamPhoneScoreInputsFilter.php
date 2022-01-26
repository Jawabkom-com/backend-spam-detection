<?php

namespace Jawabkom\Backend\Module\Spam\Detection\InputFilter;

use Jawabkom\Backend\Module\Spam\Detection\Contract\InputFilter\IInputFilter;
use Jawabkom\Backend\Module\Spam\Detection\Exception\RequiredInputsException;
use Jawabkom\Backend\Module\Spam\Detection\Exception\RequiredPhoneException;

class SpamPhoneScoreInputsFilter implements IInputFilter
{
    /**
     * @throws RequiredInputsException
     * @throws RequiredPhoneException
     */
    public function filter(array $inputs): array
    {
        if(empty($inputs)) throw new RequiredInputsException();
        if(!isset($inputs['phone'])) throw new RequiredPhoneException();

        return $inputs;
    }
}