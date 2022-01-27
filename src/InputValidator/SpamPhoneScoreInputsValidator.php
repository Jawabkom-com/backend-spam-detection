<?php

namespace Jawabkom\Backend\Module\Spam\Detection\InputValidator;

use Jawabkom\Backend\Module\Spam\Detection\Contract\InputValidator\IInputValidator;
use Jawabkom\Backend\Module\Spam\Detection\Exception\RequiredInputsException;
use Jawabkom\Backend\Module\Spam\Detection\Exception\RequiredPhoneException;
use Jawabkom\Backend\Module\Spam\Detection\Exception\ScoreNotValidException;

class SpamPhoneScoreInputsValidator implements IInputValidator
{
    /**
     * @throws RequiredInputsException
     * @throws RequiredPhoneException
     * @throws ScoreNotValidException
     */
    public function validate(array $inputs)
    {
        if(!isset($inputs['phone'])) {
            throw new RequiredPhoneException();
        }

        if(!$this->isValidScore($inputs['score'])) {
            throw new ScoreNotValidException();
        }

        throw new RequiredInputsException();

    }

    private function isValidScore($score): bool
    {
        return $score >=0 && $score <= 100;
    }
}