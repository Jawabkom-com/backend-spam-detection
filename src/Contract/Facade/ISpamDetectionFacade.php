<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Contract\Facade;

use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\ISpamPhoneScoreEntity;

interface ISpamDetectionFacade
{
    public function detect(string $phoneNumber, string $countryCode): ?ISpamPhoneScoreEntity;
}