<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Mappers;

use Jawabkom\Backend\Module\Spam\Detection\Contract\DataSource\ISpamPhoneDataSourceToEntityMapper;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\ISpamPhoneScoreEntity;
use Jawabkom\Backend\Module\Spam\Detection\Test\Classes\Entity\DummySpamPhoneScoreEntity;

class DataListMapper implements ISpamPhoneDataSourceToEntityMapper
{
    private DummySpamPhoneScoreEntity $entity;

    /**
     * @param DummySpamPhoneScoreEntity $entity
     */
    public function __construct(DummySpamPhoneScoreEntity $entity)
    {
        $this->entity = $entity;
    }

    public function map(mixed $dataSourceResult): ISpamPhoneScoreEntity
    {
        $this->entity->setPhone($dataSourceResult['phone']);
        $this->entity->setSource($dataSourceResult['source']);
        $this->entity->setScore($dataSourceResult['score']);
        $this->entity->setCountryCode($dataSourceResult['country_code']);
        $this->entity->setCreatedDateTime(new \DateTime());
        return $this->entity;
    }
}