<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Test\Unit;

use Jawabkom\Backend\Module\Spam\Detection\Exception\RequiredInputsException;
use Jawabkom\Backend\Module\Spam\Detection\Exception\RequiredPhoneException;
use Jawabkom\Backend\Module\Spam\Detection\InputFilter\AbusePhoneReportInputsFilter;
use Jawabkom\Backend\Module\Spam\Detection\InputValidator\AbusePhoneReportInputsValidator;
use Jawabkom\Backend\Module\Spam\Detection\Library\Phone;
use Jawabkom\Backend\Module\Spam\Detection\Test\AbstractTestCase;

class AbusePhoneReportValidatorTest extends AbstractTestCase
{

    public function setUp(): void
    {
        $this->validator = new AbusePhoneReportInputsValidator();
        parent::setUp();
    }

    public function test_Validator_WhenEmptyInputsArray_MustThrowException() {
        $this->expectException(RequiredInputsException::class);
        $inputs = [];
        $this->validator->validate($inputs);
    }

    public function test_Validator_WhenEmptyInputsArray_MustThrowInformativeException() {
        $this->expectException(RequiredInputsException::class);
        $this->expectExceptionMessageMatches('/reporter_id/');
        $this->expectExceptionMessageMatches('/abuse_type/');
        $this->expectExceptionMessageMatches('/phone/');
        $inputs = [];
        $this->validator->validate($inputs);
    }



}