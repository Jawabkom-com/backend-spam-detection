<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Service;

use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\IAbusePhoneReportEntity;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Repository\IAbusePhoneReportRepository;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Service\IAddAbusePhoneReportService;
use Jawabkom\Backend\Module\Spam\Detection\Library\Phone;
use Jawabkom\Standard\Abstract\AbstractService;
use Jawabkom\Standard\Contract\IDependencyInjector;

class AddAbusePhoneReportService extends AbstractService implements IAddAbusePhoneReportService
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

        $oEntity = $this->di->make(IAbusePhoneReportEntity::class);
        $oEntity->setReporterId($reporterId);
        $oEntity->setAbuseType($abuseType);
        $oEntity->setPhone($phone);
        $oEntity->setPhoneCountryCode($phoneCountry);
        $oEntity->setTags($tags);

        $oRepository = $this->di->make(IAbusePhoneReportRepository::class);
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

    private function filterInputVariables(string &$reporterId, string &$abuseType, array &$tags, string &$phone, string &$phoneCountry)
    {
        $this->filterPhoneAndCountryCode($phone, $phoneCountry);
        $reporterId = trim($reporterId);
        $abuseType = trim($abuseType);
        foreach ($tags as &$tag) {
            $tag = trim($tag);
        }
    }
}
