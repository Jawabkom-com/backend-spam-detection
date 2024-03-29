<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Service;

use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\ISpamPhoneScoreEntity;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Repository\ISpamPhoneScoreRepository;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Service\IAddUpdatePhoneSpamScoreService;
use Jawabkom\Backend\Module\Spam\Detection\Exception\RequiredCountryCodeException;
use Jawabkom\Backend\Module\Spam\Detection\Exception\RequiredPhoneException;
use Jawabkom\Backend\Module\Spam\Detection\Exception\RequiredScoreException;
use Jawabkom\Backend\Module\Spam\Detection\Exception\RequiredSourceException;
use Jawabkom\Backend\Module\Spam\Detection\Library\Phone;
use Jawabkom\Standard\Abstract\AbstractService;
use Jawabkom\Standard\Contract\IDependencyInjector;

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
     * @throws RequiredCountryCodeException
     */
    public function process(): static
    {
        $phoneRepo = $this->di->make(ISpamPhoneScoreRepository::class);

        $phone          = $this->getInput('phone');
        $source         = $this->getInput('source');
        $countryCode    = $this->getInput('countryCode');
        $score          = $this->getInput('score');
        $tags           = $this->getInput('tags');
        $meta           = $this->getInput('meta', []);

        $this->validateInputs($phone, $source, $score);

        $this->filterInputs($phone, $source, $countryCode, $score, $tags, $meta);

        $phoneEntity = $phoneRepo->getByPhoneCountryCodeAndSource($phone, $source, $countryCode);

        if(!$phoneEntity) {
            $phoneEntity = $this->di->make(ISpamPhoneScoreEntity::class);
            $phoneEntity->setPhone($phone);
            $phoneEntity->setSource($source);
            $phoneEntity->setCountryCode($countryCode);
            $phoneEntity->setCreatedDateTime(new \DateTime());
        } else {
            $phoneEntity->setUpdatedDateTime(new \DateTime());
        }
        $phoneEntity->setScore($score);
        $phoneEntity->setTags($tags);
        $phoneEntity->setMeta($meta);

        $phoneRepo->saveEntity($phoneEntity);

        $this->setOutput('result', $phoneEntity);

        return $this;
    }

    /**
     * @throws RequiredPhoneException
     * @throws RequiredSourceException
     * @throws RequiredScoreException
     */
    private function validateInputs($phone, $source, $score)
    {
        if(!$phone) throw new RequiredPhoneException();
        if(!$source) throw new RequiredSourceException();
        if(!isset($score)) throw new RequiredScoreException();
    }

    /**
     * @throws RequiredCountryCodeException
     */
    private function filterInputs(&$phone, &$source, &$countryCode, &$score, &$tags, &$meta)
    {
        $this->filterPhoneAndCountryCode($phone, $countryCode);
        $score = trim($score);
        $source = trim($source);
        $meta = (array)$meta;

        if($tags) {
            foreach ($tags as &$tag) {
                $tag = trim($tag);
            }
        }
    }

    /**
     * @throws RequiredCountryCodeException
     */
    protected function filterPhoneAndCountryCode(&$phone, &$countryCode)
    {
        $parsedPhone = $this->phoneLib->parse($phone, [$countryCode]);
        $phone = $parsedPhone['phone'];
        if ($parsedPhone['is_valid']) {
            $countryCode = $parsedPhone['country_code'];
        } else {
            if(empty($countryCode)) throw new RequiredCountryCodeException();
            $countryCode = trim($countryCode);
        }
    }

}