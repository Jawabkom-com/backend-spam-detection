<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Service;

use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\ISpamPhoneScoreEntity;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Repository\ISpamPhoneScoreRepository;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Service\IAddPhoneSpamScoreService;
use Jawabkom\Backend\Module\Spam\Detection\Exception\RequiredPhoneException;
use Jawabkom\Backend\Module\Spam\Detection\Exception\RequiredSourceException;
use Jawabkom\Standard\Abstract\AbstractService;

class AddPhoneSpamScoreService extends AbstractService implements IAddPhoneSpamScoreService
{
    public function process(): static
    {
        $phoneEntity = $this->di->make(ISpamPhoneScoreEntity::class);

        if($this->getInput('phone') == null) throw new RequiredPhoneException();
        $phoneEntity->setPhone($this->getInput('phone'));

        if($this->getInput('source') == null) throw new RequiredSourceException();
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