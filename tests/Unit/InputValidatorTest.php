<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Test\Unit;

use Jawabkom\Backend\Module\Spam\Detection\Exception\RequiredInputsException;
use Jawabkom\Backend\Module\Spam\Detection\InputValidator\SpamPhoneScoreInputsValidator;
use Jawabkom\Backend\Module\Spam\Detection\Test\AbstractTestCase;

class InputValidatorTest extends AbstractTestCase
{
    public function testSpamPhoneScoreInputs_ThrowRequiredInputsException() {
        $this->expectException(RequiredInputsException::class);
        $inputs = [
            'phone' => '', // required
            'source' => '', // required
            'country_code' => '', // required, valid country code, 2 letters
            'score' => 0,  // 0 - 100
            'tags' => [], // one dimintional array of strings, or empty
        ];

        $validator = new SpamPhoneScoreInputsValidator();
        $validator->validate($inputs);
    }
}