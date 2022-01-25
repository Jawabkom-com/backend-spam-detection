<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Contract\Repository;

use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\ISpamPhoneScoreEntity;

interface IPhoneSpamScoreRepository
{
    public function saveEntity(ISpamPhoneScoreEntity $entity):void;
}
