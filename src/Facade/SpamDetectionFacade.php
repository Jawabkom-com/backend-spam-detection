<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Facade;

use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\ISpamPhoneScoreEntity;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Facade\ISpamDetectionFacade;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Library\ISpamPhoneScoreEntitiesDigester;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Repository\ISpamPhoneScoreRepository;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Service\IGetFromDataSourceListService;
use Jawabkom\Standard\Contract\IDependencyInjector;

class SpamDetectionFacade implements ISpamDetectionFacade
{
    private IDependencyInjector $di;

    public function __construct(IDependencyInjector $di)
    {
        $this->di = $di;
    }

    public function detect(string $phoneNumber, string $countryCode, array $datasources = []): ?ISpamPhoneScoreEntity
    {
        // get matched entities from repository
        $spamPhoneScoreRepository = $this->di->make(ISpamPhoneScoreRepository::class);
        $matchedEntities = $spamPhoneScoreRepository->getByPhoneCountryCode($phoneNumber, $countryCode);

        // get matched entities from data sources
        if($datasources) {
            $getFromDatasourceListService = $this->di->make(IGetFromDataSourceListService::class);
            $serviceResult = $getFromDatasourceListService
                ->input('searchAliases', $datasources)
                ->input('phone', $phoneNumber)
                ->input('countryCode', $countryCode)
                ->process()
                ->output('result');
            if($serviceResult) {
                $matchedEntities = array_merge($matchedEntities, $serviceResult);
            }
        }

        if($matchedEntities) {
            // digest matched entities
            $entitiesDigester = $this->di->make(ISpamPhoneScoreEntitiesDigester::class);
            $newEntity = $entitiesDigester->digest($matchedEntities);
            return $newEntity;
        }

        return null;
    }

}