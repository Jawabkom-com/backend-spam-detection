<?php

namespace spec\Jawabkom\Backend\Module\Spam\Detection\Service;

use Jawabkom\Backend\Module\Spam\Detection\Contract\DataSource\IDataSourceRegistry;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\IAbusePhoneReportEntity;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Repository\IAbusePhoneReportRepository;
use Jawabkom\Backend\Module\Spam\Detection\DataSourceRegistry;
use Jawabkom\Backend\Module\Spam\Detection\Exception\RequiredInputsException;
use Jawabkom\Backend\Module\Spam\Detection\Mappers\DataListMapper;
use Jawabkom\Backend\Module\Spam\Detection\Service\AddUpdateAbusePhoneReportService;
use Jawabkom\Backend\Module\Spam\Detection\Test\Classes\DataList\TestDataListResult;
use Jawabkom\Backend\Module\Spam\Detection\Test\Classes\Entity\DummyAbusePhoneReportEntity;
use Jawabkom\Backend\Module\Spam\Detection\Test\Classes\Repository\DummyAbusePhoneReportRepository;
use Jawabkom\Standard\Contract\IDependencyInjector;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use WabLab\DI\DI;


class AddUpdateAbusePhoneReportServiceSpec extends ObjectBehavior
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
        $this->shouldHaveType(AddUpdateAbusePhoneReportService::class);
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
        $result->getCreatedDateTime()->shouldBeAnInstanceOf(\DateTime::class);
        $result->getUpdatedDateTime()->shouldBeAnInstanceOf(\DateTime::class);
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

    public function it_should_throw_exception_if_the_phone_number_is_not_normalized_and_no_country_code_provided()
    {
        $this->inputs([
            'reporter_id' => '123',
            'abuse_type' => 'spam',
            'phone' => '0788208263',
            'phone_country_code' => '',
            'tags' => ['business']
        ]);
        $this->shouldThrow(RequiredInputsException::class)->duringProcess();
    }

    public function it_should_throw_exception_if_the_phone_number_is_not_provided()
    {
        $this->inputs([
            'reporter_id' => '123',
            'abuse_type' => 'spam',
            'phone' => '',
            'phone_country_code' => 'JO',
            'tags' => ['business']
        ]);
        $this->shouldThrow(RequiredInputsException::class)->duringProcess();
    }

    public function it_should_throw_exception_if_the_reporter_id_is_not_provided()
    {
        $this->inputs([
            'reporter_id' => '',
            'abuse_type' => 'spam',
            'phone' => '+962788208263',
            'phone_country_code' => 'JO',
            'tags' => ['business']
        ]);
        $this->shouldThrow(RequiredInputsException::class)->duringProcess();
    }

    public function it_should_throw_exception_if_the_abuse_type_is_not_provided()
    {
        $this->inputs([
            'reporter_id' => '123',
            'abuse_type' => '',
            'phone' => '+962788208263',
            'phone_country_code' => 'JO',
            'tags' => ['business']
        ]);
        $this->shouldThrow(RequiredInputsException::class)->duringProcess();
    }

    public function it_should_throw_exception_if_tags_contains_none_string_item()
    {
        $this->inputs([
            'reporter_id' => '123',
            'abuse_type' => 'spam',
            'phone' => '+962788208263',
            'phone_country_code' => 'JO',
            'tags' => [['business']]
        ]);
        $this->shouldThrow(RequiredInputsException::class)->duringProcess();
    }

    public function it_should_update_abuse_report_if_all_inputs_provided()
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

        $result->getCreatedDateTime()->format('Y-m-d H:i:s')->shouldBe($result->getUpdatedDateTime()->format('Y-m-d H:i:s'));
        $result->setCreatedDateTime(new \DateTime('2000-01-01'));
        $result->setUpdatedDateTime(new \DateTime('2000-01-01'));
        $repository = new DummyAbusePhoneReportRepository();
        $repository->saveEntity($result->getWrappedObject());


        $result = $this
            ->inputs([
                'reporter_id' => '123',
                'abuse_type' => 'not_spam',
                'phone' => '+962788208263',
                'phone_country_code' => 'JO',
                'tags' => ['business']
            ])
            ->process()
            ->output('result');

        $result->getAbuseType()->shouldBe('not_spam');
        $result->getCreatedDateTime()->format('Y-m-d H:i:s')->shouldNotBe($result->getUpdatedDateTime()->format('Y-m-d H:i:s'));
    }

}
