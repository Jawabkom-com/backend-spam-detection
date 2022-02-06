<?php

namespace spec\Jawabkom\Backend\Module\Spam\Detection\Service;

use Jawabkom\Backend\Module\Spam\Detection\Contract\DataSource\IDataSourceRegistry;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\ISpamPhoneScoreEntity;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\ISearchRequestEntity;
use Jawabkom\Backend\Module\Spam\Detection\DataSourceRegistry;
use Jawabkom\Backend\Module\Spam\Detection\Exception\RequiredPhoneException;
use Jawabkom\Backend\Module\Spam\Detection\Exception\RequiredSearchAliasException;
use Jawabkom\Backend\Module\Spam\Detection\Mappers\DataListMapper;
use Jawabkom\Backend\Module\Spam\Detection\Service\GetFromDataSourceListService;
use Jawabkom\Backend\Module\Spam\Detection\Test\Classes\DataList\TestDataListResult;
use Jawabkom\Backend\Module\Spam\Detection\Test\Classes\Entity\DummySpamPhoneScoreEntity;
use Jawabkom\Backend\Module\Spam\Detection\Test\Classes\Entity\DummySearchRequestEntity;
use Jawabkom\Backend\Module\Spam\Detection\Test\Classes\Repository\DummySearchRequestRepository;
use Jawabkom\Standard\Contract\IDependencyInjector;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use WabLab\DI\DI;

class GetFromDataSourceListServiceSpec extends ObjectBehavior
{

    public function let(IDependencyInjector $di)
    {
        $wablabDi = new DI();
        $registryObj = new DataSourceRegistry();
        $registryObj->register('Test Data List', new TestDataListResult(), new DataListMapper(new DummySpamPhoneScoreEntity()));
        $registryObj->register('Another Test Data List', new TestDataListResult(), new DataListMapper(new DummySpamPhoneScoreEntity()));

        $di->make(Argument::any(), Argument::any())->will(function ($args) use ($wablabDi) {
            $alias = $args[0];
            $aliasArgs = $args[1] ?? [];
            return $wablabDi->make($alias, $aliasArgs);
        });
        $this->beConstructedWith($di->getWrappedObject(), $registryObj, new DummySearchRequestRepository(), new DummySearchRequestEntity());
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(GetFromDataSourceListService::class);
    }

    public function it_should_return_single_result_if_provided_normalized_phone()
    {
        $result = $this->inputs([
            'phone' => '+970599189357',
            'searchAliases' => ['Test Data List']
        ])->process()->output('result');

        $result->shouldHaveCount(1);
        $result->offsetGet(0)->shouldBeAnInstanceOf(ISpamPhoneScoreEntity::class);
        $result->offsetGet(0)->getPhone()->shouldBe('+970599189357');
    }

    public function it_should_return_multiple_results_if_normalized_phone_is_provided()
    {
        $result = $this->inputs([
            'phone' => '+970599189357',
            'searchAliases' => ['Test Data List', 'Another Test Data List']
        ])->process()->output('result');

        $result->shouldHaveCount(2);
        $result->offsetGet(0)->shouldBeAnInstanceOf(ISpamPhoneScoreEntity::class);
        $result->offsetGet(0)->getPhone()->shouldBe('+970599189357');
    }

    public function it_should_throw_exception_if_no_phone_provided()
    {
        $this->inputs([
            'phone' => '',
            'searchAliases' => ['Test Data List']
        ]);

        $this->shouldThrow(RequiredPhoneException::class)->duringProcess();
    }

    public function it_should_throw_exception_if_no_search_aliases_provided()
    {
        $this->inputs([
            'phone' => '+970599189357',
            'searchAliases' => []
        ]);

        $this->shouldThrow(RequiredSearchAliasException::class)->duringProcess();
    }

    public function it_should_return_single_search_request_object_when_search_done()
    {
        $this->inputs([
            'phone' => '+970599189357',
            'searchAliases' => ['Test Data List']
        ])->process()->output('search_requests')->shouldHaveCount(1);
    }

    public function it_should_return_double_search_requests_when_two_aliases_are_provided()
    {
        $this->inputs([
            'phone' => '+970599189357',
            'searchAliases' => ['Test Data List', 'Another Test Data List']
        ])->process()->output('search_requests')->shouldHaveCount(2);
    }

    public function it_should_return_status_done_when_search_for_phone()
    {
        $this->inputs([
            'phone' => '+970599189357',
            'searchAliases' => ['Test Data List']
        ])->process()->output('search_requests')->offsetGet(0)->getStatus()->shouldBe('done');
    }
}
