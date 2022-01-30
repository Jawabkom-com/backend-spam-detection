<?php

namespace Jawabkom\Backend\Module\Spam\Detection\InputValidator;

use Jawabkom\Backend\Module\Spam\Detection\Contract\InputValidator\IInputValidator;
use Jawabkom\Backend\Module\Spam\Detection\Exception\InvalidCountryCodeException;
use Jawabkom\Backend\Module\Spam\Detection\Exception\RequiredInputsException;
use Jawabkom\Backend\Module\Spam\Detection\Exception\RequiredPhoneException;
use Jawabkom\Backend\Module\Spam\Detection\Exception\InvalidScoreException;

class SpamPhoneScoreInputsValidator implements IInputValidator
{
    /**
     * @throws RequiredPhoneException
     * @throws InvalidScoreException
     * @throws InvalidCountryCodeException
     * @throws RequiredInputsException
     */
    public function validate(array $inputs)
    {
        if(!isset($inputs['phone'])) {
            throw new RequiredInputsException();
        }
        if(!$this->isValidPhoneNumber($inputs['phone'])) {
            throw new RequiredPhoneException();
        }

        if(!$this->isValidScore($inputs['score'])) {
            throw new InvalidScoreException();
        }

        if(!$this->isValidCountryCode($inputs['country_code'])) {
            throw new InvalidCountryCodeException();
        }

        //throw new RequiredInputsException();

    }

    private function isValidScore($score): bool
    {
        return $score >=0 && $score <= 100;
    }

    private function isValidCountryCode($country_code): bool
    {
        return strlen($country_code) == 2 && is_string($country_code);
    }

    private function isValidPhoneNumber($phone): bool
    {
        return !is_null($phone);
    }
}