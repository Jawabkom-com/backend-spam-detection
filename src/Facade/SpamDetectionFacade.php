<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Facade;

use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\ISpamPhoneScoreEntity;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Facade\ISpamDetectionFacade;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Library\ISpamPhoneScoreEntitiesDigester;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Repository\ISpamPhoneScoreRepository;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Service\IAddUpdatePhoneSpamScoreService;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Service\IGetFromDataSourceListService;
use Jawabkom\Standard\Contract\IDependencyInjector;

class SpamDetectionFacade implements ISpamDetectionFacade
{
    private IDependencyInjector $di;

    public function __construct(IDependencyInjector $di)
    {
        $this->di = $di;
    }

    protected function getFromDatabase(string $phoneNumber, string $countryCode):array
    {
        // get matched entities from repository
        $spamPhoneScoreRepository = $this->di->make(ISpamPhoneScoreRepository::class);
        return $spamPhoneScoreRepository->getByPhoneCountryCode($phoneNumber, $countryCode) ?? [];
    }

    protected function getFromDataSourceList(string $phoneNumber, string $countryCode, array $datasources = [], $saveOnlineResults = false):array
    {
        $matchedEntities = [];
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
                // store the result into repository
                if($matchedEntities && $saveOnlineResults) {
                    $phoneSpamScoreService = $this->di->make(IAddUpdatePhoneSpamScoreService::class);
                    foreach ($matchedEntities as $entity) {
                        $phoneSpamScoreService->inputs([
                            'phone' => $entity->getPhone(),
                            'countryCode' => $entity->getCountryCode(),
                            'source' => $entity->getSource(),
                            'score' => $entity->getScore(),
                            'tags' => []
                        ])->process();
                    }
                }
            }
        }
        return $matchedEntities;
    }

    protected function reduce(array $matchedEntities): ? ISpamPhoneScoreEntity
    {
        if($matchedEntities) {
            // digest matched entities
            $entitiesDigester = $this->di->make(ISpamPhoneScoreEntitiesDigester::class);
            return $entitiesDigester->digest($matchedEntities);
        }
        return null;
    }

    public function detect(string $phoneNumber, string $countryCode, array $datasources = [], $saveOnlineResults = false): ?ISpamPhoneScoreEntity
    {
        $matchedEntities = array_merge(
            $this->getFromDatabase($phoneNumber, $countryCode),
            $this->getFromDataSourceList($phoneNumber, $countryCode, $datasources, $saveOnlineResults)
        );
        return $this->reduce($matchedEntities);
    }

}