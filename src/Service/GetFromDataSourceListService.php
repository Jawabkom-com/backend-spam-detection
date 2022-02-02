<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Service;

use Jawabkom\Backend\Module\Spam\Detection\Contract\DataSource\IDataSourceRegistry;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\IAbusePhoneReportEntity;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\ISpamPhoneScoreEntity;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Repository\IAbusePhoneReportRepository;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Service\IGetFromDataSourceListService;
use Jawabkom\Backend\Module\Spam\Detection\DataSourceRegistry;
use Jawabkom\Backend\Module\Spam\Detection\Mappers\DataListMapper;
use Jawabkom\Backend\Module\Spam\Detection\Test\Classes\DataList\TestDataListResult;
use Jawabkom\Backend\Module\Spam\Detection\Test\Classes\DataList\TestOtherDataListResult;
use Jawabkom\Backend\Module\Spam\Detection\Test\Classes\Entity\DummyAbusePhoneReportEntity;
use Jawabkom\Backend\Module\Spam\Detection\Test\Classes\Repository\DummyAbusePhoneReportRepository;
use Jawabkom\Standard\Abstract\AbstractService;
use Jawabkom\Standard\Contract\IDependencyInjector;
use WabLab\DI\DI;

class GetFromDataSourceListService extends AbstractService implements IGetFromDataSourceListService
{
    public function __construct(IDependencyInjector $di, IDataSourceRegistry $dataSourceRegistry)
    {
        parent::__construct($di);
        $this->dataSourceRegistry = $dataSourceRegistry;
    }

    public function process(): static
    {
        $searchAliases = $this->getInput('searchAliases');
        $totalResult = [];
        foreach ($searchAliases as $alias) {
            $registryObject = $this->dataSourceRegistry->getRegistry($alias);
            $source = $registryObject['source'];
            $data = $source->getByPhone($this->getInput('phone'));
            $totalResult[] = $registryObject['mapper']->map($data);
        }
        $this->setOutput('result', $totalResult);
        return $this;
    }

}