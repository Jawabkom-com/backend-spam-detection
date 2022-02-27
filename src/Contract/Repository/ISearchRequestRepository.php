<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Contract\Repository;

use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\ISearchRequestEntity;

interface ISearchRequestRepository
{
    public function saveEntity(ISearchRequestEntity $entity):void;

    public function getByHash(string $hash, string $status);

}