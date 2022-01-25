<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Contract\DataSource;

interface ISpamPhoneDataSource
{
    public function getTTLSeconds():int;

    public function getDataSourceName():string;

    public function getByPhone(string $normalizedPhoneNumber):mixed;
}
