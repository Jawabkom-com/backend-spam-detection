<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Test\Unit;

use Jawabkom\Backend\Module\Spam\Detection\Exception\RequiredInputsException;
use Jawabkom\Backend\Module\Spam\Detection\Exception\RequiredPhoneException;
use Jawabkom\Backend\Module\Spam\Detection\InputFilter\SpamPhoneScoreInputsFilter;
use Jawabkom\Backend\Module\Spam\Detection\Library\Phone;
use Jawabkom\Backend\Module\Spam\Detection\Test\AbstractTestCase;

/**
 * @property SpamPhoneScoreInputsFilter $filterService
 */
class SpamPhoneScoreInputFilterTest extends AbstractTestCase
{

    public function setUp(): void
    {
        $this->phoneLib = new Phone();
        $this->filterService = new SpamPhoneScoreInputsFilter($this->phoneLib);
        parent::setUp(); // TODO: Change the autogenerated stub
    }

    /**
     * @throws RequiredPhoneException
     */
    public function test_SpamPhoneScore_WhenEmptyInputsArray_MustThrowException() {
        $this->expectException(RequiredInputsException::class);
        $inputs = [];
        $this->filterService->filter($inputs);
    }

    /**
     * @throws RequiredInputsException
     */
    public function test_SpamPhoneScore_WhenEmptyPhoneInput_MustThrowException() {
        $this->expectException(RequiredPhoneException::class);
        $inputs = [
            'source' => '', // required
            'country_code' => '', // required, valid country code, 2 letters
            'score' => 0,  // 0 - 100
            'tags' => [], // one dimintional array of strings, or empty
        ];
        $this->filterService->filter($inputs);
    }

    /**
     * @throws RequiredInputsException
     * @throws RequiredPhoneException
     */
    public function test_SpamPhoneScore_WhenCalledWithPhone_MustFilterToAProperFormat()
    {
        $inputs = [
            'phone' => '+970599189357',
            'country_code'=> ''
        ];
        $filteredInputs = $this->filterService->filter($inputs);
        $this->assertEquals($filteredInputs['normalizedPhone']['phone'], $inputs['phone']);
        $this->assertTrue($filteredInputs['normalizedPhone']['is_valid']);
        $this->assertEquals('PS', $filteredInputs['normalizedPhone']['country_code']);
    }

    /**
     * @throws RequiredInputsException
     * @throws RequiredPhoneException
     */
    public function test_SpamPhoneScore_WhenCalledWithZeroPrefixPhone_MustFilterToAProperFormat()
    {
        $inputs = [
            'phone' => '00970599189357',
            'country_code' => ''
        ];
        $filteredInputs = $this->filterService->filter($inputs);
        $this->assertEquals('+970599189357', $filteredInputs['normalizedPhone']['phone']);
        $this->assertTrue($filteredInputs['normalizedPhone']['is_valid']);
        $this->assertEquals('PS', $filteredInputs['normalizedPhone']['country_code']);
    }

    /**
     * @throws RequiredInputsException
     * @throws RequiredPhoneException
     */
    public function test_SpamPhoneScore_WhenCalledWithCountryCodeAndNoPrefixPhone_MustFilterToAProperFormat()
    {
        $inputs = [
            'phone' => '0599189357',
            'country_code' => 'PS'
        ];
        $filteredInputs = $this->filterService->filter($inputs);
        $this->assertEquals('+970599189357', $filteredInputs['normalizedPhone']['phone']);
        $this->assertTrue($filteredInputs['normalizedPhone']['is_valid']);
        $this->assertEquals('PS', $filteredInputs['normalizedPhone']['country_code']);
    }

    /**
     * @throws RequiredInputsException
     * @throws RequiredPhoneException
     */
    public function test_SpamPhoneScore_WhenCalledWithCountryCode_ShouldReturnsTwoLetters()
    {
        $inputs = [
            'phone' => '+970599189357',
            'country_code' => 'Palestine'
        ];

        $filteredInputs = $this->filterService->filter($inputs);
        $this->assertEquals(2, strlen($filteredInputs['normalizedPhone']['country_code']));
    }
}