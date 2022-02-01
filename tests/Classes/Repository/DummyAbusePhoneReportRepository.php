<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Test\Classes\Repository;

use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\IAbusePhoneReportEntity;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Repository\IAbusePhoneReportRepository;

class DummyAbusePhoneReportRepository implements IAbusePhoneReportRepository
{
    public static $DB = [];

    public function saveEntity(IAbusePhoneReportEntity $entity): void
    {
        $id = $this->generateEntityId($entity->getReporterId(), $entity->getPhone(), $entity->getPhoneCountryCode());
        static::$DB[$id] = $entity;
    }

    public function getByReporterIdAndPhone($reporterId, $phone, $countryCode): ?IAbusePhoneReportEntity
    {
        $id = $this->generateEntityId($reporterId, $phone, $countryCode);
        return static::$DB[$id] ?? null;
    }

    protected function generateEntityId($reporterId, $phone, $countryCode) {
        return md5("{$reporterId}-{$phone}-{$countryCode}");
    }
}