<?php

namespace Jawabkom\Backend\Module\Spam\Detection\InputFilter;

use Jawabkom\Backend\Module\Spam\Detection\Contract\InputFilter\IInputFilter;
use Jawabkom\Backend\Module\Spam\Detection\Exception\RequiredInputsException;
use Jawabkom\Backend\Module\Spam\Detection\Exception\RequiredPhoneException;
use Jawabkom\Backend\Module\Spam\Detection\Library\Phone;

class AbusePhoneReportInputsFilter implements IInputFilter
{

    public function __construct()
    {
        $this->phoneLib = new Phone();
    }

    public function filter(array $inputs): array
    {
        $phoneCountryCode = $inputs['phone_country_code'] ?? '';
        $parsedPhone = $this->phoneLib->parse($inputs['phone'] ?? '', $phoneCountryCode ? [$phoneCountryCode] : []);

        return [
            'reporter_id' => trim($inputs['reporter_id'] ?? ''),
            'abuse_type' => trim($inputs['abuse_type'] ?? ''),
            'phone' => $parsedPhone['phone'],
            'phone_country_code' => '',
            'tags' => []
        ];
    }
}