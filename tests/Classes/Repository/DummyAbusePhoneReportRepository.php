<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Test\Classes\Repository;

use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\IAbusePhoneReportEntity;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Repository\IAbusePhoneReportRepository;

class DummyAbusePhoneReportRepository implements IAbusePhoneReportRepository
{

    public static $DB = [];

    public function saveEntity(IAbusePhoneReportEntity $entity): void
    {
        static::$DB[md5($entity->getPhone().$entity->getReporterId())] = $entity;
    }
}