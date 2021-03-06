<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Test\Unit;

use Jawabkom\Backend\Module\Spam\Detection\Exception\InvalidCountryCodeException;
use Jawabkom\Backend\Module\Spam\Detection\Exception\RequiredInputsException;
use Jawabkom\Backend\Module\Spam\Detection\Exception\RequiredPhoneException;
use Jawabkom\Backend\Module\Spam\Detection\Exception\InvalidScoreException;
use Jawabkom\Backend\Module\Spam\Detection\InputValidator\SpamPhoneScoreInputsValidator;
use Jawabkom\Backend\Module\Spam\Detection\Test\AbstractTestCase;

/**
 * @property SpamPhoneScoreInputsValidator $inputValidator
 */
class SpamPhoneScoreInputValidatorTest extends AbstractTestCase
{
    public function setUp(): void
    {
        $this->inputValidator = new SpamPhoneScoreInputsValidator();
        parent::setUp(); // TODO: Change the autogenerated stub
    }

//    public function test_Validate_WhenCalledWithEmpty_MustThrowRequiredInputsException() {
//        $this->expectException(RequiredInputsException::class);
//        $inputs = [
//            'phone' => '', // required
//            'source' => '', // required
//            'country_code' => '', // required, valid country code, 2 letters
//            'score' => 0,  // 0 - 100
//            'tags' => [], // one dimensional array of strings, or empty
//        ];
//
//        $this->inputValidator->validate($inputs);
//    }
//
//    public function test_Validate_WhenCalledWithEmptyPhone_MustThrowPhoneIsRequiredException()
//    {
//        $this->expectException(RequiredPhoneException::class);
//        $inputs = [
//            'source' => '', // required
//            'country_code' => '', // required, valid country code, 2 letters
//            'score' => 0,  // 0 - 100
//            'tags' => [], // one dimensional array of strings, or empty
//        ];
//
//        $this->inputValidator->validate($inputs);
//    }
//
//    public function test_Validate_WhenCalledWithInvalidScore_MustThrowScoreException()
//    {
//        $this->expectException(InvalidScoreException::class);
//
//        $inputs = [
//            'phone' => '+970599189357',
//            'score'=> -10
//        ];
//
//        $this->inputValidator->validate($inputs);
//    }
//
//    public function test_Validate_WhenCalledWithInvalidCountryCode_MustThrowCountryCodeException()
//    {
//        $this->expectException(InvalidCountryCodeException::class);
//
//        $inputs = [
//            'phone' => '+970599189357',
//            'score'=> 10,
//            'country_code' => null
//        ];
//
//        $this->inputValidator->validate($inputs);
//    }
}