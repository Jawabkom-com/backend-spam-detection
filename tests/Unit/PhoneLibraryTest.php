<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Test\Unit;

use Jawabkom\Backend\Module\Spam\Detection\Library\Phone;
use Jawabkom\Backend\Module\Spam\Detection\Test\AbstractTestCase;

/**
 * @property Phone $phoneLib
 */
class PhoneLibraryTest extends AbstractTestCase
{
    public function setUp(): void
    {
        $this->phoneLib = new Phone();
        parent::setUp(); // TODO: Change the autogenerated stub
    }

    public function testParseInvalidPhone()
    {
        $parse = $this->phoneLib->parse('555');
        $this->assertEquals('555', $parse['phone']);
        $this->assertFalse($parse['is_valid']);
        $this->assertNull($parse['country_code']);
    }

    public function testParseNormalizedPhoneNumber()
    {
        $parse = $this->phoneLib->parse('+962788208263');
        $this->assertEquals('+962788208263', $parse['phone']);
        $this->assertTrue($parse['is_valid']);
        $this->assertEquals('JO', $parse['country_code']);
    }

    public function testParseNormalizedPhoneNumberWith00()
    {
        $parse = $this->phoneLib->parse('00962788208263');
        $this->assertEquals('+962788208263', $parse['phone']);
        $this->assertTrue($parse['is_valid']);
        $this->assertEquals('JO', $parse['country_code']);
    }

    public function testParseNormalizedPhoneNumber_NoPrefix()
    {
        $parse = $this->phoneLib->parse('962788208263');
        $this->assertEquals('+962788208263', $parse['phone']);
        $this->assertTrue($parse['is_valid']);
        $this->assertEquals('JO', $parse['country_code']);
    }

    public function testLocalPhoneNumberWithInvalidCountryCode()
    {
        $parse = $this->phoneLib->parse('078-8208263', ['IN', 'UK']);
        $this->assertEquals('0788208263', $parse['phone']);
        $this->assertFalse($parse['is_valid']);
        $this->assertNull($parse['country_code']);
    }

    public function testLocalPhoneNumberWithPossibleCountryCodes()
    {
        $parse = $this->phoneLib->parse('078 820 8263', ['SA', 'AE', 'JO']);
        $this->assertEquals('+962788208263', $parse['phone']);
        $this->assertTrue($parse['is_valid']);
        $this->assertEquals('JO', $parse['country_code']);
    }

    public function testArabicLetters_LocalPhoneNumberWithPossibleCountryCodes()
    {
        $parse = $this->phoneLib->parse('٠٧٨٨٢٠٨٢٦٣', ['SA', 'AE', 'JO']);
        $this->assertEquals('+962788208263', $parse['phone']);
        $this->assertTrue($parse['is_valid']);
        $this->assertEquals('JO', $parse['country_code']);
    }
    public function testParseInvalidPhoneNumberException()
    {
        $phone= $this->phoneLib->parse('+9627882082631');
        $this->assertEquals(false,$phone['is_valid']);
    }

}