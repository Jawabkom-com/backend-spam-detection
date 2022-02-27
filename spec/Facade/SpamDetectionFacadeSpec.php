<?php

namespace spec\Jawabkom\Backend\Module\Spam\Detection\Facade;
use Jawabkom\Backend\Module\Spam\Detection\Contract\DataSource\IDataSourceRegistry;
use Jawabkom\Backend\Module\Spam\Detection\Contract\DataSource\ISpamPhoneDataSource;
use Jawabkom\Backend\Module\Spam\Detection\Contract\DataSource\ISpamPhoneDataSourceToEntityMapper;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\ISearchRequestEntity;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\ISpamPhoneScoreEntity;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Library\ISpamPhoneScoreEntitiesDigester;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Repository\ISearchRequestRepository;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Repository\ISpamPhoneScoreRepository;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Service\IAddUpdatePhoneSpamScoreService;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Service\IGetFromDataSourceListService;
use Jawabkom\Backend\Module\Spam\Detection\Service\GetFromDataSourceListService;
use Jawabkom\Backend\Module\Spam\Detection\Test\Classes\Entity\DummySearchRequestEntity;
use Jawabkom\Backend\Module\Spam\Detection\Test\Classes\Entity\DummySpamPhoneScoreEntity;
use Jawabkom\Standard\Contract\IDependencyInjector;
use PhpSpec\ObjectBehavior;
use PhpSpec\Wrapper\Collaborator;
use Prophecy\Argument;
use Prophecy\Prophet;
use WabLab\DI\DI;

class SpamDetectionFacadeSpec extends ObjectBehavior
{
    public DI $wablabDi;

    public function __construct()
    {
        $this->wablabDi = new DI();
        $this->wablabDi->register(ISpamPhoneScoreEntity::class, DummySpamPhoneScoreEntity::class);
        $this->wablabDi->register(IGetFromDataSourceListService::class, GetFromDataSourceListService::class);
        $this->wablabDi->register(ISearchRequestEntity::class, DummySearchRequestEntity::class);

    }

    public function let(IDependencyInjector $di)
    {
        $tmpDi = $this->wablabDi;
        $di->make(Argument::any(), Argument::any())->will(function ($args) use ($tmpDi) {
            $alias = $args[0];
            $aliasArgs = $args[1] ?? [];
            return $tmpDi->make($alias, $aliasArgs);
        });
        $diObj = $di->getWrappedObject();
        $this->wablabDi->register(IDependencyInjector::class, $diObj);
        $this->beConstructedWith($diObj);
    }

    public function it_should_return_single_phone_spam_score_entity_if_matches_found_in_database_first(ISpamPhoneScoreRepository $repository, ISpamPhoneScoreEntitiesDigester $digester)
    {
        $this->registerMockedRepositoryWithTwoEntitiesToReturn($repository);
        $this->registerMockedDigester($digester);

        $entity = $this->detect('+962788888888', 'JO');
        $entity->shouldBeAnInstanceOf(ISpamPhoneScoreEntity::class);
        $entity->getPhone()->shouldBe('+962788888888');
        $entity->getCountryCode()->shouldBe('JO');
        $entity->getScore()->shouldBe(100.00);
    }

    public function it_should_return_null_if_no_matches_found_in_database(ISpamPhoneScoreRepository $repository, ISpamPhoneScoreEntitiesDigester $digester)
    {
        $this->registerMockedRepositoryWithNoEntitiesToReturn($repository);
        $this->registerMockedDigester($digester);

        $entity = $this->detect('+962788888888', 'JO');
        $entity->shouldBeNull();
    }

    public function it_should_merge_online_search_results_with_offline_results_when_searcher_aliases_provided(ISpamPhoneScoreRepository $repository, ISpamPhoneScoreEntitiesDigester $digester,
                                                                                                              IDataSourceRegistry $registry, ISearchRequestRepository $searchRequestRepository)
    {
        $this->registerMockedRepositoryWithTwoEntitiesToReturn($repository);
        $this->registerMockedDigester($digester);
        $this->registerMockedDataSourceRegister($registry);
        $this->registerMockedSearchRequestRepository($searchRequestRepository);

        $entity = $this->detect('+962788888888', 'JO', ['test_searcher_alias1', 'test_searcher_alias2']);
        $entity->getScore()->shouldBe(200.00);
    }

    public function it_should_store_search_results_in_the_database(ISpamPhoneScoreRepository $repository, ISpamPhoneScoreEntitiesDigester $digester,
                                                            IDataSourceRegistry $registry, ISearchRequestRepository $searchRequestRepository,
                                                                   IAddUpdatePhoneSpamScoreService $phoneSpamScoreService)
    {
        $this->registerMockedRepositoryWithTwoEntitiesToReturn($repository);
        $this->registerMockedDigester($digester);
        $this->registerMockedDataSourceRegister($registry);
        $this->registerMockedSearchRequestRepository($searchRequestRepository);
        $this->registerMockedPhoneSpamScoreService($phoneSpamScoreService);
        $entity = $this->detect('+962788888888', 'JO', ['test_searcher_alias1', 'test_searcher_alias2']);
        $entity->getScore()->shouldBe(200.00);
    }

    //
    // mock creators
    //

    protected function registerMockedRepositoryWithTwoEntitiesToReturn(ISpamPhoneScoreRepository|Collaborator $repository): void
    {
        $repository->getByPhoneCountryCode('+962788888888', 'JO')->will(function () {
            $entity1 = new DummySpamPhoneScoreEntity();
            $entity1->setPhone('+962788888888');
            $entity1->setCountryCode('JO');
            $entity1->setScore(50);
            $entity1->setSource('dummy_source1');

            $entity2 = new DummySpamPhoneScoreEntity();
            $entity2->setPhone('+962788888888');
            $entity2->setCountryCode('JO');
            $entity2->setScore(50);
            $entity2->setSource('dummy_source2');

            return [$entity1, $entity2];
        });
        $this->wablabDi->register(ISpamPhoneScoreRepository::class, $repository->getWrappedObject());
    }

    protected function registerMockedRepositoryWithNoEntitiesToReturn(ISpamPhoneScoreRepository|Collaborator $repository): void
    {
        $repository->getByPhoneCountryCode('+962788888888', 'JO')->will(function () {
            return null;
        });
        $this->wablabDi->register(ISpamPhoneScoreRepository::class, $repository->getWrappedObject());
    }

    protected function registerMockedDigester(ISpamPhoneScoreEntitiesDigester|Collaborator $digester): void
    {
        $digester->digest(Argument::any())->will(function ($args) {
            /**@var $entities \Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\ISpamPhoneScoreEntity[] */
            $entities = $args[0];
            $newEntity = new DummySpamPhoneScoreEntity();
            $newEntity->setScore(0);
            foreach ($entities as $entity) {
                $newEntity->setPhone($entity->getPhone());
                $newEntity->setCountryCode($entity->getCountryCode());
                $newEntity->setScore($newEntity->getScore() + $entity->getScore());
                $newEntity->setSource('merged');
            }
            return $newEntity;
        });
        $this->wablabDi->register(ISpamPhoneScoreEntitiesDigester::class, $digester->getWrappedObject());
    }

    protected function registerMockedPhoneSpamScoreService(IAddUpdatePhoneSpamScoreService|Collaborator $service):void
    {
        $this->wablabDi->register(IAddUpdatePhoneSpamScoreService::class, $service->getWrappedObject());
    }

    protected function registerMockedDataSourceRegister(IDataSourceRegistry|Collaborator $registry):void
    {
        $registry->getRegistry('test_searcher_alias1')->will(function(){
                $ph = new Prophet();
                $searcher = $ph->prophesize(ISpamPhoneDataSource::class);

                $searcher->getByPhone('+962788888888', 'JO')->willReturn([
                    'phone' => '+962788888888',
                    'country_code' => 'JO',
                    'tags' => null,
                    'score' => 50,
                    'source' => 'test_searcher_alias1'
                ]);

                $mapperObj = $ph->prophesize(ISpamPhoneDataSourceToEntityMapper::class);

                $mapperObj->map([
                    'phone' => '+962788888888',
                    'country_code' => 'JO',
                    'tags' => null,
                    'score' => 50,
                    'source' => 'test_searcher_alias1'
                ])->will(function(){
                    $entity = new DummySpamPhoneScoreEntity();
                    $entity->setPhone('+962788888888');
                    $entity->setCountryCode('JO');
                    $entity->setScore(50);
                    $entity->setSource('test_searcher_alias1');
                    return $entity;
                });

                return [
                    'source' => $searcher,
                    'mapper' => $mapperObj
                ];
        });
        $registry->getRegistry('test_searcher_alias2')->will(function(){
            $ph = new Prophet();
            $searcher = $ph->prophesize(ISpamPhoneDataSource::class);
            $searcher->getByPhone('+962788888888', 'JO')->willReturn([
                'phone' => '+962788888888',
                'country_code' => 'JO',
                'tags' => null,
                'score' => 50,
                'source' => 'test_searcher_alias2'
            ]);

            $mapperObj = $ph->prophesize(ISpamPhoneDataSourceToEntityMapper::class);
            $mapperObj->map([
                'phone' => '+962788888888',
                'country_code' => 'JO',
                'tags' => null,
                'score' => '50',
                'source' => 'test_searcher_alias2'
            ])->will(function(){
                $entity = new DummySpamPhoneScoreEntity();
                $entity->setPhone('+962788888888');
                $entity->setCountryCode('JO');
                $entity->setScore(50);
                $entity->setSource('test_searcher_alias2');
                return $entity;
            });

            return [
                'source' => $searcher,
                'mapper' => $mapperObj
            ];
        });
        $this->wablabDi->register(IDataSourceRegistry::class, $registry->getWrappedObject());
    }

    protected function registerMockedSearchRequestRepository(ISearchRequestRepository|Collaborator $searchRequestRepository):void
    {
        $this->wablabDi->register(ISearchRequestRepository::class, $searchRequestRepository->getWrappedObject());
    }

}
