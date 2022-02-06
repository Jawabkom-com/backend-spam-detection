<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Test\Classes\DataList;

use Jawabkom\Backend\Module\Spam\Detection\Contract\DataSource\ISpamPhoneDataSource;

class TestDataListResult implements ISpamPhoneDataSource
{

    public function getTTLSeconds(): int
    {
        return 3600;
    }

    public function getDataSourceName(): string
    {
        return 'Test Data List';
    }

    public function getByPhone(string $normalizedPhoneNumber): mixed
    {
        return json_decode(<<<JSON
                { "id": 1,
                "name": "Mamdouh Zaqout",
                "username": "Mamdouh",
                "score": 20.0,
                "country_code": "PS",
                "source": "Test Date List",
                "email": "mamdouhzaq@gmail.com",
                "phone": "{$normalizedPhoneNumber}"
                }
                JSON, true);
    }
}