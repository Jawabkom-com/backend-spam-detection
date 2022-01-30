<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Test\Unit;

use Jawabkom\Backend\Module\Spam\Detection\Exception\RequiredInputsException;
use Jawabkom\Backend\Module\Spam\Detection\Exception\RequiredPhoneException;
use Jawabkom\Backend\Module\Spam\Detection\InputFilter\AbusePhoneReportInputsFilter;
use Jawabkom\Backend\Module\Spam\Detection\Library\Phone;
use Jawabkom\Backend\Module\Spam\Detection\Test\AbstractTestCase;

class AbusePhoneReportFilterTest extends AbstractTestCase
{

    private AbusePhoneReportInputsFilter $filter;

    public function setUp(): void
    {
        $this->filter = new AbusePhoneReportInputsFilter();
        parent::setUp();
    }

    public function test_Filter_WhenEmptyInputs_MustReturnArrayWithDefaultValues()
    {
        $filtered = $this->filter->filter([]);
        $this->assertEquals($filtered['reporter_id'], '');
        $this->assertEquals($filtered['abuse_type'], '');
        $this->assertEquals($filtered['phone'], '');
        $this->assertEquals($filtered['phone_country_code'], '');
        $this->assertEquals($filtered['tags'], []);
    }

    public function test_Filter_WhenReporterIdNotTrimmed_MustBeTrimmed()
    {
        $filtered = $this->filter->filter(['reporter_id' => ' rid ']);
        $this->assertEquals($filtered['reporter_id'], 'rid');
    }

    public function test_Filter_WhenAbuseTypeNotTrimmed_MustBeTrimmed()
    {
        $filtered = $this->filter->filter(['abuse_type' => ' type ']);
        $this->assertEquals($filtered['abuse_type'], 'type');
    }

    public function test_Filter_WhenNormalizedPhoneNumberNotFormatted_MustBeFormatted()
    {
        $filtered = $this->filter->filter(['phone' => ' 00(962) 78 - 820 82 99 ']);
        $this->assertEquals($filtered['phone'], '+962788208299');
    }

    public function test_Filter_WhenUnnormalizedAndInvalidPhoneNumberNotFormatted_MustBeFormatted()
    {

    }

    public function test_Filter_WhenUnnormalizedAndValidPhoneNumberNotFormatted_MustBeFormatted()
    {

    }

//
//
//    /**
//     * @throws RequiredInputsException
//     */
//    public function test_Filter_WhenEmptyPhoneInput_MustThrowException() {
//        $this->expectException(RequiredPhoneException::class);
//        $inputs = [
//            'source' => '', // required
//            'country_code' => '', // required, valid country code, 2 letters
//            'score' => 0,  // 0 - 100
//            'tags' => [], // one dimintional array of strings, or empty
//        ];
//        $this->filterService->filter($inputs);
//    }
//
//    /**
//     * @throws RequiredInputsException
//     * @throws RequiredPhoneException
//     */
//    public function test_Filter_WhenCalledWithPhone_MustFilterToAProperFormat()
//    {
//        $inputs = [
//            'phone' => '+970599189357',
//            'country_code'=> ''
//        ];
//        $filteredInputs = $this->filterService->filter($inputs);
//        $this->assertEquals($filteredInputs['normalizedPhone']['phone'], $inputs['phone']);
//        $this->assertTrue($filteredInputs['normalizedPhone']['is_valid']);
//        $this->assertEquals('PS', $filteredInputs['normalizedPhone']['country_code']);
//    }
//
//    /**
//     * @throws RequiredInputsException
//     * @throws RequiredPhoneException
//     */
//    public function test_Filter_WhenCalledWithZeroPrefixPhone_MustFilterToAProperFormat()
//    {
//        $inputs = [
//            'phone' => '00970599189357',
//            'country_code' => ''
//        ];
//        $filteredInputs = $this->filterService->filter($inputs);
//        $this->assertEquals('+970599189357', $filteredInputs['normalizedPhone']['phone']);
//        $this->assertTrue($filteredInputs['normalizedPhone']['is_valid']);
//        $this->assertEquals('PS', $filteredInputs['normalizedPhone']['country_code']);
//    }
//
//    /**
//     * @throws RequiredInputsException
//     * @throws RequiredPhoneException
//     */
//    public function test_Filter_WhenCalledWithCountryCodeAndNoPrefixPhone_MustFilterToAProperFormat()
//    {
//        $inputs = [
//            'phone' => '0599189357',
//            'country_code' => 'PS'
//        ];
//        $filteredInputs = $this->filterService->filter($inputs);
//        $this->assertEquals('+970599189357', $filteredInputs['normalizedPhone']['phone']);
//        $this->assertTrue($filteredInputs['normalizedPhone']['is_valid']);
//        $this->assertEquals('PS', $filteredInputs['normalizedPhone']['country_code']);
//    }
//
//    /**
//     * @throws RequiredInputsException
//     * @throws RequiredPhoneException
//     */
//    public function test_Filter_WhenCalledWithCountryCode_ShouldReturnsTwoLetters()
//    {
//        $inputs = [
//            'phone' => '+970599189357',
//            'country_code' => 'Palestine'
//        ];
//
//        $filteredInputs = $this->filterService->filter($inputs);
//        $this->assertEquals(2, strlen($filteredInputs['normalizedPhone']['country_code']));
//    }
}