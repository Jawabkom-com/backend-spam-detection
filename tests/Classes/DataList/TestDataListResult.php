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
        return 'source1';
    }

    public function getByPhone(string $normalizedPhoneNumber, ?string $countryCode = null): mixed
    {
        return json_decode(<<<JSON
                { "id": 1,
                "name": "Mamdouh Mohammed Zaqout",
                "username": "Mamdouh",
                "score": 20.0,
                "country_code": "PS",
                "source": "source1",
                "email": "mamdouhzaq@hotmail.com",
                "phone": "{$normalizedPhoneNumber}"
                }
                JSON, true);
    }
}