<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Service;

use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\ISpamPhoneScoreEntity;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Service\IGetFromDataSourceListService;
use Jawabkom\Backend\Module\Spam\Detection\Test\Classes\DataList\TestDataListResult;
use Jawabkom\Standard\Abstract\AbstractService;

class GetFromDataSourceListService extends AbstractService implements IGetFromDataSourceListService
{

    public function process(): static
    {
        $testDataList = $this->di->make(TestDataListResult::class);
        $response = $testDataList->getByPhone($this->getInput('phone'));
        $this->outputs['result'] = $response;
        return $this;
    }

}