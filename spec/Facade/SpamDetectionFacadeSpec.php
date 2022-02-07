<?php

namespace spec\Jawabkom\Backend\Module\Spam\Detection\Facade;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\ISpamPhoneScoreEntity;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Library\ISpamPhoneScoreEntitiesDigester;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Repository\ISpamPhoneScoreRepository;
use Jawabkom\Backend\Module\Spam\Detection\DataSourceRegistry;
use Jawabkom\Backend\Module\Spam\Detection\Test\Classes\DataList\TestDataListResult;
use Jawabkom\Backend\Module\Spam\Detection\Test\Classes\Entity\DummySearchRequestEntity;
use Jawabkom\Backend\Module\Spam\Detection\Test\Classes\Entity\DummySpamPhoneScoreEntity;
use Jawabkom\Standard\Contract\IDependencyInjector;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use WabLab\DI\DI;

class SpamDetectionFacadeSpec extends ObjectBehavior
{
    public DI $wablabDi;

    public function __construct()
    {
        $this->wablabDi = new DI();
        $this->wablabDi->register(ISpamPhoneScoreEntity::class, DummySpamPhoneScoreEntity::class);
        //static::$wablabDi->register(ISpamPhoneScoreRepository::class, DummySpamPhoneScoreRepository::class);
    }

    public function let(IDependencyInjector $di)
    {
        $tmpDi = $this->wablabDi;
        $di->make(Argument::any(), Argument::any())->will(function ($args) use ($tmpDi) {
            $alias = $args[0];
            $aliasArgs = $args[1] ?? [];
            return $tmpDi->make($alias, $aliasArgs);
        });
        $this->beConstructedWith($di->getWrappedObject());
    }

    public function it_should_return_single_phone_spam_score_entity_if_matches_found_in_database_first(ISpamPhoneScoreRepository $repository, ISpamPhoneScoreEntitiesDigester $digester)
    {
        $this->registerMockedRepositoryWithTwoEntitiesToReturn($repository);
        $this->registerMockedDigister($digester);

        $entity = $this->detect('+962788208282', 'JO');
        $entity->shouldBeAnInstanceOf(ISpamPhoneScoreEntity::class);
        $entity->getPhone()->shouldBe('+962788208282');
        $entity->getCountryCode()->shouldBe('JO');
        $entity->getScore()->shouldBe(140.00);
    }

    public function it_should_return_null_if_no_matches_found_in_database(ISpamPhoneScoreRepository $repository, ISpamPhoneScoreEntitiesDigester $digester)
    {
        $this->registerMockedRepositoryWithNoEntitiesToReturn($repository);
        $this->registerMockedDigister($digester);

        $entity = $this->detect('+962788208282', 'JO');
        $entity->shouldBeNull();
    }

    protected function registerMockedRepositoryWithTwoEntitiesToReturn(ISpamPhoneScoreRepository|\PhpSpec\Wrapper\Collaborator $repository): void
    {
        $repository->getByPhoneCountryCode('+962788208282', 'JO')->will(function () {
            $entity1 = new DummySpamPhoneScoreEntity();
            $entity1->setPhone('+962788208282');
            $entity1->setCountryCode('JO');
            $entity1->setScore(50);
            $entity1->setSource('dummy_source1');

            $entity2 = new DummySpamPhoneScoreEntity();
            $entity2->setPhone('+962788208282');
            $entity2->setCountryCode('JO');
            $entity2->setScore(90);
            $entity2->setSource('dummy_source2');

            return [$entity1, $entity2];
        });
        $this->wablabDi->register(ISpamPhoneScoreRepository::class, $repository->getWrappedObject());
    }

    protected function registerMockedRepositoryWithNoEntitiesToReturn(ISpamPhoneScoreRepository|\PhpSpec\Wrapper\Collaborator $repository): void
    {
        $repository->getByPhoneCountryCode('+962788208282', 'JO')->will(function () {
            return null;
        });
        $this->wablabDi->register(ISpamPhoneScoreRepository::class, $repository->getWrappedObject());
    }

    protected function registerMockedDigister(ISpamPhoneScoreEntitiesDigester|\PhpSpec\Wrapper\Collaborator $digester): void
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
}
