<?php

namespace spec\Jawabkom\Backend\Module\Spam\Detection\Service;

use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\IAbusePhoneReportEntity;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Repository\IAbusePhoneReportRepository;
use Jawabkom\Backend\Module\Spam\Detection\Service\AddAbusePhoneReportService;
use Jawabkom\Backend\Module\Spam\Detection\Test\Classes\Entity\DummyAbusePhoneReportEntity;
use Jawabkom\Backend\Module\Spam\Detection\Test\Classes\Repository\DummyAbusePhoneReportRepository;
use Jawabkom\Standard\Contract\IDependencyInjector;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use WabLab\DI\DI;


class AddAbusePhoneReportServiceSpec extends ObjectBehavior
{

    public function let(IDependencyInjector $di)
    {
        $wablabDi = new DI();
        $wablabDi->register(IAbusePhoneReportEntity::class, DummyAbusePhoneReportEntity::class);
        $wablabDi->register(IAbusePhoneReportRepository::class, DummyAbusePhoneReportRepository::class);

        $di->make(Argument::any(), Argument::any())->will(function ($args) use($wablabDi) {
            $alias = $args[0];
            $aliasArgs = $args[1] ?? [];
            return $wablabDi->make($alias, $aliasArgs);
        });
        DummyAbusePhoneReportRepository::$DB = [];
        $this->beConstructedWith($di->getWrappedObject());
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(AddAbusePhoneReportService::class);
    }

    public function it_should_create_abuse_report_if_all_inputs_provided()
    {
        $result = $this
            ->inputs([
                'reporter_id' => '123',
                'abuse_type' => 'spam',
                'phone' => '+962788208263',
                'phone_country_code' => 'JO',
                'tags' => ['business']
            ])
            ->process()
            ->output('result');

        $result->shouldBeAnInstanceOf(IAbusePhoneReportEntity::class);
        $result->getReporterId()->shouldBe('123');
        $result->getAbuseType()->shouldBe('spam');
        $result->getPhone()->shouldBe('+962788208263');
        $result->getPhoneCountryCode()->shouldBe('JO');
        $result->getTags()->shouldHaveCount(1);
    }

    public function it_should_autofill_the_country_code_if_the_phone_number_is_normalized()
    {
        $result = $this
            ->inputs([
                'reporter_id' => '123',
                'abuse_type' => 'spam',
                'phone' => '+962788208263',
                'phone_country_code' => 'SA',
                'tags' => ['business']
            ])
            ->process()
            ->output('result')
            ->getPhoneCountryCode()->shouldBe('JO');
    }

    public function it_should_auto_format_the_phone_number_if_the_phone_number_is_not_normalized()
    {
        $result = $this
            ->inputs([
                'reporter_id' => '123',
                'abuse_type' => 'spam',
                'phone' => '0788208263',
                'phone_country_code' => 'JO',
                'tags' => ['business']
            ])
            ->process()
            ->output('result')
            ->getPhone()->shouldBe('+962788208263');
    }


    public function it_should_trim_all_inputs_if_all_inputs_provided_with_spaces()
    {
        $result = $this
            ->inputs([
                'reporter_id' => '    123   ',
                'abuse_type' => '    spam    ',
                'phone' => '    +962788208263    ',
                'phone_country_code' => '    JO    ',
                'tags' => ['    business    ']
            ])
            ->process()
            ->output('result');

        $result->shouldBeAnInstanceOf(IAbusePhoneReportEntity::class);
        $result->getReporterId()->shouldBe('123');
        $result->getAbuseType()->shouldBe('spam');
        $result->getPhone()->shouldBe('+962788208263');
        $result->getPhoneCountryCode()->shouldBe('JO');
        $result->getTags()->offsetGet(0)->shouldBe('business');
    }
}
