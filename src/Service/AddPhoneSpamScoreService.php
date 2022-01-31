<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Service;

use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\ISpamPhoneScoreEntity;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Repository\ISpamPhoneScoreRepository;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Service\IAddPhoneSpamScoreService;
use Jawabkom\Backend\Module\Spam\Detection\Exception\RequiredPhoneException;
use Jawabkom\Backend\Module\Spam\Detection\Exception\RequiredSourceException;
use Jawabkom\Backend\Module\Spam\Detection\Library\Phone;
use Jawabkom\Standard\Abstract\AbstractService;
use Jawabkom\Standard\Contract\IDependencyInjector;

class AddPhoneSpamScoreService extends AbstractService implements IAddPhoneSpamScoreService
{
    private Phone $phoneLib;

    public function __construct(IDependencyInjector $di)
    {
        parent::__construct($di);
        $this->phoneLib = $di->make(Phone::class);
    }

    /**
     * @throws RequiredPhoneException
     * @throws RequiredSourceException
     */
    public function process(): static
    {
        $phoneEntity = $this->di->make(ISpamPhoneScoreEntity::class);

        if($this->getInput('phone') == null) throw new RequiredPhoneException();
        if($this->getInput('source') == null) throw new RequiredSourceException();

        $phone = trim($this->getInput('phone'));
        $source = trim($this->getInput('source'));
        $country_code = $this->getInput('country_code');

        $parsedPhone = $this->phoneLib->parse($phone, [$country_code]);
        $phone = $parsedPhone['phone'];
        if ($parsedPhone['is_valid']) {
            $country_code = $parsedPhone['country_code'];
        } else {
            $country_code = trim($country_code);
        }

        $score = trim($this->getInput('score'));

        $tags = $this->getInput('tags');

        foreach ($tags as &$tag) {
            $tag = trim($tag);
        }

        $phoneEntity->setSource($source);
        $phoneEntity->setPhone($phone);
        $phoneEntity->setCountryCode($country_code);
        $phoneEntity->setScore($score);
        $phoneEntity->setTags($tags);

        $phoneRepo = $this->di->make(ISpamPhoneScoreRepository::class);
        $phoneRepo->saveEntity($phoneEntity);

        $this->setOutput('result', $phoneEntity);

        return $this;
    }

}