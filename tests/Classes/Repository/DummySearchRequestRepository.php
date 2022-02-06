<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Test\Classes\Repository;

use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\ISearchRequestEntity;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Repository\ISearchRequestRepository;
use Jawabkom\Backend\Module\Spam\Detection\Test\Classes\Entity\DummySearchRequestEntity;

class DummySearchRequestRepository implements ISearchRequestRepository
{
    public static $DB = [];
    public static $ID = 0;

    public function saveEntity(ISearchRequestEntity $entity):void
    {
        static::$DB[++static::$ID] = $entity;
    }

    public function getByHash(string $hash, string $status)
    {
        return static::$DB[static::$ID]->$hash ?? null;
    }

}