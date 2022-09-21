<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Facade;

use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\ISpamPhoneScoreEntity;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Facade\ISpamDetectionFacade;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Library\ISpamPhoneScoreEntitiesDigester;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Queue\IQueuePusher;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Repository\ISpamPhoneScoreRepository;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Service\IAddUpdatePhoneSpamScoreService;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Service\IGetFromDataSourceListService;
use Jawabkom\Backend\Module\Spam\Detection\Library\Phone;
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
                    $this->saveMatchedScoreEntities($matchedEntities);
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

    public function detect(string $phoneNumber, string $countryCode, array $datasources = [], $saveOnlineResults = false, IQueuePusher $onlineSearchRequestsQueue = null): ?ISpamPhoneScoreEntity
    {
        $phoneLib = new Phone();
        $parsedPhone = $phoneLib->parse($phoneNumber, [$countryCode]);
        $normalizedPhone = $parsedPhone['phone'];
        $countryCode = $parsedPhone['country_code'] ?? $countryCode;
        $matchedEntities = $this->getFromDatabase($normalizedPhone, $countryCode);
        if($onlineSearchRequestsQueue) {
            $this->onlineSearchToQueue($datasources, $onlineSearchRequestsQueue, $phoneNumber, $countryCode, $saveOnlineResults);
        } else {
            $datasourceResults = $this->getFromDataSourceList($normalizedPhone, $countryCode, $datasources, $saveOnlineResults);
            $matchedEntities = array_merge($matchedEntities, $datasourceResults);
        }

        return $this->reduce($matchedEntities);
    }

    protected function onlineSearchToQueue(array $datasources, IQueuePusher $onlineSearchRequestsQueue, string $phoneNumber, mixed $countryCode, mixed $saveOnlineResults): void
    {
        foreach ($datasources as $datasource) {
            $onlineSearchRequestsQueue->push([
                'phone' => $phoneNumber,
                'country_code' => $countryCode,
                'data_source' => $datasource,
                'save_result' => $saveOnlineResults
            ]);
        }
    }

    protected function saveMatchedScoreEntities(array $matchedEntities): void
    {
        $phoneSpamScoreService = $this->di->make(IAddUpdatePhoneSpamScoreService::class);
        foreach ($matchedEntities as $entity) {
            $phoneSpamScoreService->inputs([
                'phone' => $entity->getPhone(),
                'countryCode' => $entity->getCountryCode(),
                'source' => $entity->getSource(),
                'score' => $entity->getScore(),
                'tags' => $entity->getTags(),
                'meta' => $entity->getMeta()
            ])->process();
        }
    }

}