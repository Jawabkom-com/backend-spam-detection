<?php

namespace spec\Jawabkom\Backend\Module\Spam\Detection\Service;

use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\ISpamPhoneScoreEntity;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\ISearchRequestEntity;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Repository\ISpamPhoneScoreRepository;
use Jawabkom\Backend\Module\Spam\Detection\DataSourceRegistry;
use Jawabkom\Backend\Module\Spam\Detection\Exception\RequiredInputsException;
use Jawabkom\Backend\Module\Spam\Detection\Mappers\DataListMapper;
use Jawabkom\Backend\Module\Spam\Detection\Service\GetFromDataSourceListService;
use Jawabkom\Backend\Module\Spam\Detection\Test\Classes\DataList\TestDataListResult;
use Jawabkom\Backend\Module\Spam\Detection\Test\Classes\DataList\TestOtherDataListResult;
use Jawabkom\Backend\Module\Spam\Detection\Test\Classes\Entity\DummySpamPhoneScoreEntity;
use Jawabkom\Backend\Module\Spam\Detection\Test\Classes\Entity\DummySearchRequestEntity;
use Jawabkom\Backend\Module\Spam\Detection\Test\Classes\Repository\DummySearchRequestRepository;
use Jawabkom\Backend\Module\Spam\Detection\Test\Classes\Repository\DummySpamPhoneScoreRepository;
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
        $registryObj->register('source1', new TestDataListResult(), new DataListMapper(new DummySpamPhoneScoreEntity()));
        $registryObj->register('source2', new TestOtherDataListResult(), new DataListMapper(new DummySpamPhoneScoreEntity()));
        $wablabDi->register(ISearchRequestEntity::class, DummySearchRequestEntity::class);

        $di->make(Argument::any(), Argument::any())->will(function ($args) use ($wablabDi) {
            $alias = $args[0];
            $aliasArgs = $args[1] ?? [];
            return $wablabDi->make($alias, $aliasArgs);
        });
        DummySearchRequestRepository::$DB = [];
        $this->beConstructedWith($di->getWrappedObject(), $registryObj, new DummySearchRequestRepository(), new DummySpamPhoneScoreRepository());
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(GetFromDataSourceListService::class);
    }

    public function it_should_return_single_result_if_provided_normalized_phone()
    {
        $result = $this->inputs([
            'phone' => '+970599189357',
            'countryCode' => 'PS',
            'searchAliases' => ['source1']
        ])->process()->output('result');

        $result->shouldHaveCount(1);
        $result->offsetGet(0)->shouldBeAnInstanceOf(ISpamPhoneScoreEntity::class);
        $result->offsetGet(0)->shouldBeAnInstanceOf(ISpamPhoneScoreEntity::class);
        $result->offsetGet(0)->getPhone()->shouldBe('+970599189357');
    }

    public function it_should_return_multiple_results_if_normalized_phone_is_provided()
    {
        $result = $this->inputs([
            'phone' => '+970599189357',
            'countryCode' => 'PS',
            'searchAliases' => ['source1', 'source2']
        ])->process()->output('result');

        $result->shouldHaveCount(2);
        $result->offsetGet(0)->shouldBeAnInstanceOf(ISpamPhoneScoreEntity::class);
        $result->offsetGet(0)->getPhone()->shouldBe('+970599189357');
    }

    public function it_should_throw_exception_if_no_phone_provided()
    {
        $this->inputs([
            'phone' => '',
            'countryCode' => 'PS',
            'searchAliases' => ['source1']
        ]);

        $this->shouldThrow(RequiredInputsException::class)->duringProcess();
    }

    public function it_should_throw_exception_if_no_search_aliases_provided()
    {
        $this->inputs([
            'phone' => '+970599189357',
            'countryCode' => 'PS',
            'searchAliases' => []
        ]);

        $this->shouldThrow(RequiredInputsException::class)->duringProcess();
    }

    public function it_should_return_single_search_request_object_when_search_done()
    {
        $this->inputs([
            'phone' => '+970599189357',
            'countryCode' => 'PS',
            'searchAliases' => ['source1']
        ])->process()->output('search_requests')->shouldHaveCount(1);
    }

    public function it_should_return_double_search_requests_when_two_aliases_are_provided()
    {
        $this->inputs([
            'phone' => '+970599189357',
            'countryCode' => 'PS',
            'searchAliases' => ['source1', 'source2']
        ])->process()->output('search_requests')->shouldHaveCount(2);
    }

    public function it_should_return_status_done_when_search_for_phone()
    {
        $this->inputs([
            'phone' => '+970599189357',
            'countryCode' => 'PS',
            'searchAliases' => ['source1']
        ])->process()->output('search_requests')->offsetGet('source1')->getStatus()->shouldBe('done');
    }

    public function it_should_return_right_hash_value_if_no_data_missing()
    {
        $result = $this->inputs([
            'phone' => '+970599189357',
            'countryCode' => 'PS',
            'searchAliases' => ['source1']
        ])->process()->output('search_requests');

        $result->offsetGet('source1')->getHash()->shouldBe(md5(json_encode(['phone' => '+970599189357', 'countryCode' => 'PS'])));
        $result->offsetGet('source1')->getIsFromCache()->shouldBe(false);
    }

    public function it_should_add_result_for_search_requests_into_cache()
    {
        $searchAliases = ['source1', 'source2'];
        $phone = '+970599189357';
        $countryCode = 'PS';

        $hash = md5(json_encode(['phone' => $phone, 'countryCode' => $countryCode]));

        $result = $this->inputs([
            'phone' => $phone,
            'countryCode' => 'PS',
            'searchAliases' => $searchAliases
        ])->process()->output('search_requests')->offsetGet('source1');

        $result->getHash()->shouldBe($hash);
        $result->getIsFromCache()->shouldBe(false);

//        $this->inputs([
//            'phone' => $phone,
//            'countryCode' => 'PS',
//            'searchAliases' => $searchAliases
//        ])->process()->output('search_requests')->offsetGet(0)->getIsFromCache()->shouldBe(true);

    }

    public function it_should_return_search_request_record_if_data_was_received()
    {
        $searchAliases = ['source1', 'source2'];
        $phone = '+970599189357';
        $countryCode = 'PS';

        $results = $this->inputs([
            'phone' => $phone,
            'countryCode' => $countryCode,
            'searchAliases' => $searchAliases
        ])->process()->output('search_requests')->offsetGet('source1')->shouldBeAnInstanceOf(ISearchRequestEntity::class);
    }

    public function it_should_get_data_from_cache_when_service_requested()
    {
        $searchAliases = ['source1', 'source2'];
        $phone = '+970599189357';
        $countryCode = 'PS';

        $this->inputs([
            'phone' => $phone,
            'countryCode' => $countryCode,
            'searchAliases' => $searchAliases
        ])->process()->output('result');

        $this->inputs([
            'phone' => $phone,
            'countryCode' => $countryCode,
            'searchAliases' => $searchAliases
        ])->process()->output('search_requests')->offsetGet('source1')->getIsFromCache()->shouldBe(true);
    }

    public function it_should_store_phone_score_record_when_data_is_provided(ISpamPhoneScoreRepository $phoneScoreRepository)
    {
        $searchAliases = ['source1', 'source2'];
        $phone = '+970599189357';
        $countryCode = 'PS';

        $result = $this->inputs([
            'phone' => $phone,
            'countryCode' => $countryCode,
            'searchAliases' => $searchAliases
        ])->process()->output('result')->offsetGet(0)->getWrappedObject();

        $entity = new DummySpamPhoneScoreEntity();
        $entity->setPhone($result->getPhone());
        $entity->setSource($result->getSource());
        $entity->setScore($result->getScore());
        $entity->setCountryCode($result->getCountryCode());
        $entity->setCreatedDateTime($result->getCreatedDateTime());

        $phoneScoreRepository->saveEntity($entity)->shouldBeCalled();
        //$record = $phoneScoreRepository->getByPhoneCountryCodeAndSource($phone, 'source1', $countryCode);
        //var_dump($record);
    }

}
