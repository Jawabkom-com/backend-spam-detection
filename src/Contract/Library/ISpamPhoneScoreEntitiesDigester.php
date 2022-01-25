<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Contract\Library;

use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\ISpamPhoneScoreEntity;

interface ISpamPhoneScoreEntitiesDigester
{

    /**
     * @param ISpamPhoneScoreEntity[] $entities
     * @return ISpamPhoneScoreEntity
     */
    public function digest(array $entities): ISpamPhoneScoreEntity;
}
