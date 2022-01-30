<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Test\Functional;

use Jawabkom\Backend\Module\Spam\Detection\Service\AddPhoneSpamScoreService;
use Jawabkom\Backend\Module\Spam\Detection\Test\AbstractTestCase;

class PhoneSpamScoreTest extends AbstractTestCase
{
    public function test_AddService_WhenGivenAllData_MustReturnsRecord() {
        $phoneSpamService = new AddPhoneSpamScoreService();
        $inputs = [
            'phone' => '+970599189357',
            'score' => 10,
            'source' => 'test',
            'country_code' => 'PS',
            'tags' => []
        ];
        $result = $phoneSpamService->inputs($inputs)->process()->output('result');
        $this->assertNotEmpty($result);
        $this->assertEquals('+970599189357', $result['phone']);
    }


//    public function test_CreateNewRecord() {
//        $store = new Store('table1', $this->dbPath, $this->dbConfig);
//        $store->insert(['id' => 10, 'name' => 'ahmad']);
//        $this->assertTrue(true);
//    }

}