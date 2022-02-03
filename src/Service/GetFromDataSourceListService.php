<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Service;

use Jawabkom\Backend\Module\Spam\Detection\Contract\DataSource\IDataSourceRegistry;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\IAbusePhoneReportEntity;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\ISpamPhoneScoreEntity;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Repository\IAbusePhoneReportRepository;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Service\IGetFromDataSourceListService;
use Jawabkom\Backend\Module\Spam\Detection\DataSourceRegistry;
use Jawabkom\Backend\Module\Spam\Detection\Exception\RequiredPhoneException;
use Jawabkom\Backend\Module\Spam\Detection\Exception\RequiredSearchAliasException;
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
    private IDataSourceRegistry $dataSourceRegistry;

    public function __construct(IDependencyInjector $di, IDataSourceRegistry $dataSourceRegistry)
    {
        parent::__construct($di);
        $this->dataSourceRegistry = $dataSourceRegistry;
    }

    /**
     * @throws RequiredPhoneException
     * @throws RequiredSearchAliasException
     */
    public function process(): static
    {
        $searchAliases = $this->getInput('searchAliases');
        $phone = $this->getInput('phone');

        $this->validateInputs($searchAliases, $phone);

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

    /**
     * @throws RequiredPhoneException
     * @throws RequiredSearchAliasException
     */
    private function validateInputs($searchAliases, $phone)
    {
        if(empty($searchAliases)) throw new RequiredSearchAliasException('Search aliases are required, please provide one at minimum');
        if($phone == '') throw new RequiredPhoneException('Phone is required');
    }

}