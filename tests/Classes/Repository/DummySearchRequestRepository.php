<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Test\Classes\Repository;

use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\ISearchRequestEntity;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Repository\ISearchRequestRepository;
use Jawabkom\Backend\Module\Spam\Detection\Test\Classes\Entity\DummySearchRequestEntity;

class DummySearchRequestRepository implements ISearchRequestRepository
{
    public static $DB = [];

    public function saveEntity(ISearchRequestEntity $entity):void
    {
        static::$DB[$entity->getHash()] = $entity;
    }

    public function getByHash(string $hash): iterable
    {
        return static::$DB[$hash] ?? [];
    }
}