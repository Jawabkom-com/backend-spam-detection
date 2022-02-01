<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Test\Classes\Repository;

use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\ISpamPhoneScoreEntity;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Repository\ISpamPhoneScoreRepository;

class DummySpamPhoneScoreRepository implements ISpamPhoneScoreRepository
{

    public static $DB = [];

    public function saveEntity(ISpamPhoneScoreEntity $entity): void
    {
        static::$DB[md5($entity->getPhone().$entity->getSource())] = $entity;
    }

    public function getByPhoneAndSource($phone, $source, $countryCode): ?ISpamPhoneScoreEntity
    {
        return static::$DB[md5($phone.$source)] ?? null;
    }

}