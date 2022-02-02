<?php

namespace spec\Jawabkom\Backend\Module\Spam\Detection\Service;

use Jawabkom\Backend\Module\Spam\Detection\Contract\DataSource\IDataSourceRegistry;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\ISpamPhoneScoreEntity;
use Jawabkom\Backend\Module\Spam\Detection\DataSourceRegistry;
use Jawabkom\Backend\Module\Spam\Detection\Mappers\DataListMapper;
use Jawabkom\Backend\Module\Spam\Detection\Service\GetFromDataSourceListService;
use Jawabkom\Backend\Module\Spam\Detection\Test\Classes\DataList\TestDataListResult;
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
        $registryObj->register('Test Data List', new TestDataListResult(), new DataListMapper());
        $registryObj->register('Another Test Data List', new TestDataListResult(), new DataListMapper());
        $di->make(Argument::any(), Argument::any())->will(function ($args) use($wablabDi) {
            $alias = $args[0];
            $aliasArgs = $args[1] ?? [];
            return $wablabDi->make($alias, $aliasArgs);
        });
        $this->beConstructedWith($di->getWrappedObject(), $registryObj);
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
}
