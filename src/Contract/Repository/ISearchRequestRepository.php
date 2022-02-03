<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Contract\Repository;

use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\ISearchRequestEntity;
use Jawabkom\Backend\Module\Spam\Detection\Test\Classes\Entity\DummySearchRequestEntity;

interface ISearchRequestRepository
{
    public function saveEntity(ISearchRequestEntity $entity):void;

    /**
     * @return ISearchRequestEntity[]
     */
    public function getByHash(string $hash):iterable;
}