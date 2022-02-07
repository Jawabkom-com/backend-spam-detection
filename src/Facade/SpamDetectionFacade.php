<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Facade;

use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\ISpamPhoneScoreEntity;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Facade\ISpamDetectionFacade;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Library\ISpamPhoneScoreEntitiesDigester;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Repository\ISpamPhoneScoreRepository;
use Jawabkom\Standard\Contract\IDependencyInjector;

class SpamDetectionFacade implements ISpamDetectionFacade
{
    private IDependencyInjector $di;

    public function __construct(IDependencyInjector $di)
    {
        $this->di = $di;
    }

    public function detect(string $phoneNumber, string $countryCode): ?ISpamPhoneScoreEntity
    {
        // get matched entities
        $spamPhoneScoreRepository = $this->di->make(ISpamPhoneScoreRepository::class);
        $matchedEntities = $spamPhoneScoreRepository->getByPhoneCountryCode($phoneNumber, $countryCode);

        if($matchedEntities) {
            // digest matched entities
            $entitiesDigester = $this->di->make(ISpamPhoneScoreEntitiesDigester::class);
            $newEntity = $entitiesDigester->digest($matchedEntities);
            return $newEntity;
        }

        return null;
    }

}