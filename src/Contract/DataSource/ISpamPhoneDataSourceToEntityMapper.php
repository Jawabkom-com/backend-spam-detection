<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Contract\DataSource;

use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\ISpamPhoneScoreEntity;

interface ISpamPhoneDataSourceToEntityMapper
{
    public function map(mixed $dataSourceResult):ISpamPhoneScoreEntity;
}
