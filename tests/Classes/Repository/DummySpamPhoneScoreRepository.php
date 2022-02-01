<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Test\Classes\Repository;

use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\ISpamPhoneScoreEntity;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Repository\ISpamPhoneScoreRepository;

class DummySpamPhoneScoreRepository implements ISpamPhoneScoreRepository
{

    public static $DB = [];

    public function saveEntity(ISpamPhoneScoreEntity $entity): void
    {
        $id = $this->generateEntityId($entity->getPhone(), $entity->getSource(), $entity->getCountryCode());
        static::$DB[$id] = $entity;
    }

    public function getByPhoneCountryCodeAndSource($phone, $source, $countryCode): ?ISpamPhoneScoreEntity
    {
        $id = $this->generateEntityId($phone,$source, $countryCode);
        return static::$DB[$id] ?? null;
    }

    protected function generateEntityId($phone,$source, $countryCode) {
        return md5("{$phone}-{$source}-{$countryCode}");
    }
}