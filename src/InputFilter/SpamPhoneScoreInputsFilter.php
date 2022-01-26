<?php

namespace Jawabkom\Backend\Module\Spam\Detection\InputFilter;

use Jawabkom\Backend\Module\Spam\Detection\Contract\InputFilter\IInputFilter;
use Jawabkom\Backend\Module\Spam\Detection\Exception\RequiredInputsException;
use Jawabkom\Backend\Module\Spam\Detection\Exception\RequiredPhoneException;
use Jawabkom\Backend\Module\Spam\Detection\Library\Phone;

class SpamPhoneScoreInputsFilter implements IInputFilter
{
    private Phone $phoneLib;

    /**
     * @param Phone $phoneLib
     */
    public function __construct(Phone $phoneLib)
    {
        $this->phoneLib = $phoneLib;
    }
    /**
     * @throws RequiredInputsException
     * @throws RequiredPhoneException
     */
    public function filter(array $inputs): array
    {
        if(empty($inputs)) throw new RequiredInputsException();
        if(!isset($inputs['phone'])) throw new RequiredPhoneException();

        $result = [];
        $result['normalizedPhone'] =  $this->phoneLib->parse($inputs['phone']);
        return $result;
    }
}