<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Contract\Repository;

use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\IAbusePhoneReportEntity;

interface IAbusePhoneReportRepository
{
    public function saveEntity(IAbusePhoneReportEntity $entity):void;

    public function getByReporterIdAndPhone($reporterId, $phone, $countryCode):?IAbusePhoneReportEntity;
}
