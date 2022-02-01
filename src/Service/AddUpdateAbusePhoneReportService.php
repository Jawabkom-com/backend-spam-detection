<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Service;

use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\IAbusePhoneReportEntity;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Repository\IAbusePhoneReportRepository;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Service\IAddUpdateAbusePhoneReportService;
use Jawabkom\Backend\Module\Spam\Detection\Exception\RequiredInputsException;
use Jawabkom\Backend\Module\Spam\Detection\Library\Phone;
use Jawabkom\Standard\Abstract\AbstractService;
use Jawabkom\Standard\Contract\IDependencyInjector;

class AddUpdateAbusePhoneReportService extends AbstractService implements IAddUpdateAbusePhoneReportService
{
    private Phone $phoneLib;

    public function __construct(IDependencyInjector $di)
    {
        parent::__construct($di);
        $this->phoneLib = $di->make(Phone::class);
    }

    public function process(): static
    {
        $reporterId = $this->getInput('reporter_id');
        $abuseType = $this->getInput('abuse_type');
        $phone = $this->getInput('phone');
        $phoneCountry = $this->getInput('phone_country_code');
        $tags = $this->getInput('tags');

        $this->filterInputVariables($reporterId, $abuseType, $tags, $phone, $phoneCountry);
        $this->validateInputVariables($reporterId, $abuseType, $tags, $phone, $phoneCountry);

        $oRepository = $this->di->make(IAbusePhoneReportRepository::class);

        $oEntity = $oRepository->getByReporterIdAndPhone($reporterId, $phone, $phoneCountry);
        if(!$oEntity) {
            $oEntity = $this->di->make(IAbusePhoneReportEntity::class);
            $oEntity->setReporterId($reporterId);
            $oEntity->setPhone($phone);
            $oEntity->setPhoneCountryCode($phoneCountry);
            $oEntity->setCreatedDateTime(new \DateTime());
        }
        $oEntity->setAbuseType($abuseType);
        $oEntity->setTags($tags);
        $oEntity->setUpdatedDateTime(new \DateTime());

        $oRepository->saveEntity($oEntity);

        $this->setOutput('result', $oEntity);
        return $this;
    }

    protected function filterPhoneAndCountryCode(string &$phone, string &$phoneCountry): void
    {
        $parsedPhone = $this->phoneLib->parse($phone, [$phoneCountry]);
        $phone = $parsedPhone['phone'];
        if ($parsedPhone['is_valid']) {
            $phoneCountry = $parsedPhone['country_code'];
        } else {
            $phoneCountry = trim($phoneCountry);
        }
    }

    protected function filterInputVariables(string &$reporterId, string &$abuseType, array &$tags, string &$phone, string &$phoneCountry)
    {
        $this->filterPhoneAndCountryCode($phone, $phoneCountry);
        $reporterId = trim($reporterId);
        $abuseType = trim($abuseType);
        foreach ($tags as &$tag) {
            if(is_string($tag))
                $tag = trim($tag);
        }
    }

    protected function validateInputVariables(string $reporterId, string $abuseType, array $tags, string $phone, string $phoneCountry)
    {
        $this->validatePhoneCountryInput($phoneCountry);
        $this->validatePhoneInput($phone);
        $this->validateReporterIdInput($reporterId);
        $this->validateAbuseTypeInput($abuseType);
        $this->validateTagsInput($tags);
    }

    protected function validatePhoneCountryInput(string $phoneCountry): void
    {
        if (!$phoneCountry) {
            throw new RequiredInputsException('Phone country code must be provided');
        }
    }

    protected function validatePhoneInput(string $phone): void
    {
        if (!$phone) {
            throw new RequiredInputsException('Phone number must be provided');
        }
    }

    protected function validateReporterIdInput(string $reporterId): void
    {
        if (!$reporterId) {
            throw new RequiredInputsException('Reporter id must be provided');
        }
    }

    protected function validateAbuseTypeInput(string $abuseType): void
    {
        if (!$abuseType) {
            throw new RequiredInputsException('Abuse type must be provided');
        }
    }

    protected function validateTagsInput(array $tags): void
    {
        if ($tags) {
            foreach ($tags as $tag) {
                if (!is_string($tag)) {
                    throw new RequiredInputsException('Tags accepts only list of strings');
                }
            }
        }
    }
}
