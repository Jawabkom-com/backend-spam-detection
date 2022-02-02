<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Mappers;

use Jawabkom\Backend\Module\Spam\Detection\Contract\DataSource\ISpamPhoneDataSourceToEntityMapper;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\ISpamPhoneScoreEntity;
use Jawabkom\Backend\Module\Spam\Detection\Test\Classes\Entity\DummySpamPhoneScoreEntity;
use Jawabkom\Backend\Module\Spam\Detection\Test\Classes\Repository\DummySpamPhoneScoreRepository;

class DataListMapper implements ISpamPhoneDataSourceToEntityMapper
{

    public function map(mixed $dataSourceResult): ISpamPhoneScoreEntity
    {
        $mRepository = new DummySpamPhoneScoreRepository();
        $mEntity = new DummySpamPhoneScoreEntity();
        $mEntity->setPhone($dataSourceResult['phone']);
        $mEntity->setSource($dataSourceResult['source']);
        $mEntity->setScore($dataSourceResult['score']);
        $mEntity->setCountryCode($dataSourceResult['country_code']);
        $mEntity->setCreatedDateTime(new \DateTime());
        $mRepository->saveEntity($mEntity);
        return $mEntity;
    }
}