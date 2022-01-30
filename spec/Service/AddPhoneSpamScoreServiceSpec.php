<?php

namespace spec\Jawabkom\Backend\Module\Spam\Detection\Service;

use Jawabkom\Backend\Module\Spam\Detection\Exception\RequiredInputsException;
use Jawabkom\Backend\Module\Spam\Detection\Service\AddAbusePhoneReportService;
use Jawabkom\Backend\Module\Spam\Detection\Service\AddPhoneSpamScoreService;
use PhpSpec\ObjectBehavior;

class AddPhoneSpamScoreServiceSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(AddPhoneSpamScoreService::class);
    }

    function it_should_create_phone_spam_record_if_all_inputs_provided()
    {
        $this->inputs([
            'phone' => '+970599189357',
            'score' => 10,
            'source' => 'test',
            'country_code' => 'PS',
            'tags' => []
        ])->process()->output('result')->shouldHaveKeyWithValue('phone', '+970599189357');
    }

    function it_should_throw_exception_if_any_input_is_missing()
    {
        $this->shouldThrow(RequiredInputsException::class)->duringProcess();
    }

}
