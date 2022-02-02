<?php

namespace Jawabkom\Backend\Module\Spam\Detection;

use Jawabkom\Backend\Module\Spam\Detection\Contract\DataSource\IDataSourceRegistry;
use Jawabkom\Backend\Module\Spam\Detection\Contract\DataSource\ISpamPhoneDataSource;
use Jawabkom\Backend\Module\Spam\Detection\Contract\DataSource\ISpamPhoneDataSourceToEntityMapper;

class DataSourceRegistry implements IDataSourceRegistry
{
    protected array $registry = [];

    public function register(string $alias, ISpamPhoneDataSource $source, ISpamPhoneDataSourceToEntityMapper $mapper)
    {
        $this->registry[$alias]['source'] = $source;
        $this->registry[$alias]['mapper'] = $mapper;
    }

    public function getRegistry(string $alias)
    {
        return $this->registry[$alias];
    }

    public function getSource(string $alias): ISpamPhoneDataSource
    {
        return $this->registry[$alias]['source'];
    }

    public function getMapper(string $alias): ISpamPhoneDataSourceToEntityMapper
    {
        return $this->registry[$alias]['mapper'];
    }
}