<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Service;

use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\ISpamPhoneScoreEntity;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Repository\ISpamPhoneScoreRepository;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Service\IAddUpdatePhoneSpamScoreService;
use Jawabkom\Backend\Module\Spam\Detection\Exception\RequiredPhoneException;
use Jawabkom\Backend\Module\Spam\Detection\Exception\RequiredScoreException;
use Jawabkom\Backend\Module\Spam\Detection\Exception\RequiredSourceException;
use Jawabkom\Backend\Module\Spam\Detection\Library\Phone;
use Jawabkom\Backend\Module\Spam\Detection\Test\Classes\Entity\DummySpamPhoneScoreEntity;
use Jawabkom\Backend\Module\Spam\Detection\Test\Classes\Repository\DummySpamPhoneScoreRepository;
use Jawabkom\Standard\Abstract\AbstractService;
use Jawabkom\Standard\Contract\IDependencyInjector;
use WabLab\DI\DI;

class AddUpdatePhoneSpamScoreService extends AbstractService implements IAddUpdatePhoneSpamScoreService
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
     * @throws RequiredScoreException
     */
    public function process(): static
    {
        $phoneEntity = $this->di->make(ISpamPhoneScoreEntity::class);

        $phone          = $this->getInput('phone');
        $source         = $this->getInput('source');
        $country_code   = $this->getInput('country_code');
        $score          = $this->getInput('score');
        $tags           = $this->getInput('tags');

        $this->validateInputs($phone, $source, $score);

        $this->filterInputs($phone, $source, $country_code, $score, $tags);

        // check if record already exists
        //$this->checkIfRecordExists($phone, $source);

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

    private function checkIfRecordExists($phone, $source)
    {

    }

    /**
     * @throws RequiredPhoneException
     * @throws RequiredSourceException
     * @throws RequiredScoreException
     */
    private function validateInputs($phone, $source, $score)
    {
        if($phone == null) throw new RequiredPhoneException();
        if($source == null) throw new RequiredSourceException();
        if($score == null) throw new RequiredScoreException();
    }

    private function filterInputs(&$phone, &$source, &$country_code, &$score, &$tags)
    {
        $this->filterPhoneAndCountryCode($phone, $country_code);
        $score = trim($score);
        $source = trim($source);

        foreach ($tags as &$tag) {
            $tag = trim($tag);
        }
    }

    protected function filterPhoneAndCountryCode(&$phone, &$country_code)
    {
        $parsedPhone = $this->phoneLib->parse($phone, [$country_code]);
        $phone = $parsedPhone['phone'];
        if ($parsedPhone['is_valid']) {
            $country_code = $parsedPhone['country_code'];
        } else {
            $country_code = trim($country_code);
        }
    }

}