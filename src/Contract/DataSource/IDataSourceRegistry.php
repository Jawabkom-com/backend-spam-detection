<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Contract\DataSource;

interface IDataSourceRegistry
{
    public function register(string $alias, ISpamPhoneDataSource $source, ISpamPhoneDataSourceToEntityMapper $mapper);
    public function getRegistry(string $alias);
    public function getSource(string $alias);
    public function getMapper(string $alias);

}