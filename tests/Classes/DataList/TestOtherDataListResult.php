<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Test\Classes\DataList;

use Jawabkom\Backend\Module\Spam\Detection\Contract\DataSource\ISpamPhoneDataSource;

class TestOtherDataListResult implements ISpamPhoneDataSource
{

    public function getTTLSeconds(): int
    {
        return 5000;
    }

    public function getDataSourceName(): string
    {
        return 'Another Test Data List';
    }

    public function getByPhone(string $normalizedPhoneNumber): mixed
    {
        return json_decode(<<<JSON
                { "id": 1,
                "name": "Mamdouh Zaqout",
                "username": "Mamdouh",
                "email": "mamdouhzaq@gmail.com",
                "phone": "{$normalizedPhoneNumber}",
                "spam": "No"
                }
                JSON, true);
    }
}