<?php

namespace spec\Jawabkom\Backend\Module\Spam\Detection\Service;

use Jawabkom\Backend\Module\Spam\Detection\Contract\DataSource\ISpamPhoneDataSource;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Service\IGetFromDataSourceListService;
use Jawabkom\Backend\Module\Spam\Detection\Service\GetFromDataSourceListService;
use Jawabkom\Standard\Contract\IDependencyInjector;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use WabLab\DI\DI;

class GetFromDataSourceListServiceSpec extends ObjectBehavior
{

    public function let(IDependencyInjector $di)
    {
        $wablabDi = new DI();

        $di->make(Argument::any(), Argument::any())->will(function ($args) use($wablabDi) {
            $alias = $args[0];
            $aliasArgs = $args[1] ?? [];
            return $wablabDi->make($alias, $aliasArgs);
        });
        $this->beConstructedWith($di->getWrappedObject());
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(GetFromDataSourceListService::class);
    }

    public function it_should_return_result_if_provided_normalized_phone()
    {
//        $result = $this->inputs([
//            'phone' => '+970599189357',
//        ])->process()->output('result');
//
//        $result->shouldBeAnInstanceOf(IGetFromDataSourceListService::class);
    }
}
