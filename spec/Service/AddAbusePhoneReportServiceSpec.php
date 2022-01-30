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
use Prophecy\Prophet;
use WabLab\DI\DI;

class AddAbusePhoneReportServiceSpec extends ObjectBehavior
{
    private Prophet $prophet;
    private DI $wablabDi;

    public function __construct()
    {
        $this->prophet = new Prophet();
        $this->wablabDi = new DI();
        $this->wablabDi->register(IAbusePhoneReportEntity::class, DummyAbusePhoneReportEntity::class);
        $this->wablabDi->register(IAbusePhoneReportRepository::class, DummyAbusePhoneReportRepository::class);
    }

    function let(IDependencyInjector $di)
    {
        $di->make(Argument::any(), Argument::any())->will(function ($args) {
            $alias = $args[0];
            $aliasArgs = $args[1] ?? [];
            return $this->wablabDi->make($alias, $aliasArgs);
        });
        DummyAbusePhoneReportRepository::$DB = [];
        $this->beConstructedWith($di->getWrappedObject());
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(AddAbusePhoneReportService::class);
    }

    function it_should_create_abuse_report_if_all_inputs_provided()
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
    }

}
