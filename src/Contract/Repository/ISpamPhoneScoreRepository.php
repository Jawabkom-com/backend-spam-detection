<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Contract\Repository;

use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\ISpamPhoneScoreEntity;

interface ISpamPhoneScoreRepository
{
    public function saveEntity(ISpamPhoneScoreEntity $entity):void;
    public function getByPhoneCountryCodeAndSource($phone, $source, $countryCode): ?ISpamPhoneScoreEntity;
}
