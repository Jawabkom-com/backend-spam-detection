<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Service;

use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\IAbusePhoneReportEntity;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Repository\IAbusePhoneReportRepository;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Service\IAddAbusePhoneReportService;
use Jawabkom\Standard\Abstract\AbstractService;
use Jawabkom\Standard\Contract\IDependencyInjector;

class AddAbusePhoneReportService extends AbstractService implements IAddAbusePhoneReportService
{

    public function process(): static
    {
        $oEntity = $this->di->make(IAbusePhoneReportEntity::class);
        $oEntity->setReporterId($this->getInput('reporter_id'));
        $oEntity->setAbuseType($this->getInput('abuse_type'));
        $oEntity->setPhone($this->getInput('phone'));
        $oEntity->setPhoneCountryCode($this->getInput('phone_country_code'));
        $oEntity->setTags($this->getInput('tags'));

        $oRepository = $this->di->make(IAbusePhoneReportRepository::class);
        $oRepository->saveEntity($oEntity);

        $this->setOutput('result', $oEntity);
        return $this;
    }
}
