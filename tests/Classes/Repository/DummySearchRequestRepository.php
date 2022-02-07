<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Test\Classes\Repository;

use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\ISearchRequestEntity;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Repository\ISearchRequestRepository;

class DummySearchRequestRepository implements ISearchRequestRepository
{
    public static array $DB = [];

    public function saveEntity(ISearchRequestEntity $entity):void
    {
        static::$DB[$entity->getHash()][] = $entity;
    }

    public function getByHash(string $hash, string $status)
    {
        return static::$DB[$hash] ?? null;
    }

    public function getAll()
    {
        return static::$DB;
    }

}