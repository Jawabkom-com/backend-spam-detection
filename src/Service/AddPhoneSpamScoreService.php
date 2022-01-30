<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Service;

use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\ISpamPhoneScoreEntity;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Repository\ISpamPhoneScoreRepository;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Service\IAddPhoneSpamScoreService;
use Jawabkom\Backend\Module\Spam\Detection\Exception\RequiredInputsException;
use Jawabkom\Standard\Abstract\AbstractService;
use SleekDB\Store;

class AddPhoneSpamScoreService extends AbstractService implements IAddPhoneSpamScoreService
{
    public function process(): static
    {
        $phoneEntity = $this->di->make(ISpamPhoneScoreEntity::class);
        $phoneEntity->setPhone($this->getInput('phone'));
        $phoneEntity->setSource($this->getInput('source'));
        $phoneEntity->setCountryCode($this->getInput('country_code'));
        $phoneEntity->setScore($this->getInput('score'));
        $phoneEntity->setTags($this->getInput('tags'));

        $phoneRepo = $this->di->make(ISpamPhoneScoreRepository::class);
        $phoneRepo->saveEntity($phoneEntity);

        $this->setOutput('result', $phoneEntity);

        return $this;
    }

}