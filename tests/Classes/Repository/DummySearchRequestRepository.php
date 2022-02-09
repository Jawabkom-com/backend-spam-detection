<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Test\Classes\Repository;

use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\ISearchRequestEntity;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Repository\ISearchRequestRepository;

class DummySearchRequestRepository implements ISearchRequestRepository
{
    public static array $DB = [];

    public function saveEntity(ISearchRequestEntity $entity):void
    {
        $id = $this->generateEntityId($entity->getHash(), $entity->getResultAliasSource());
        static::$DB['pk_index'][$id] = $entity;
        static::$DB['hash_index'][$entity->getHash()][$entity->getResultAliasSource()] = $entity;
    }

    public function getByHash(string $hash, string $status)
    {
        $result = static::$DB['hash_index'][$hash] ?? null;
        if($result)
            return array_values($result);
        return null;
    }

    protected function generateEntityId($hash,$source) {
        return md5("{$hash}-{$source}");
    }

    public function getAll()
    {
        return static::$DB;
    }
}