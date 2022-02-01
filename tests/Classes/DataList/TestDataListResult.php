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
                "name": "Leanne Graham",
                "username": "Bret",
                "email": "Sincere@april.biz",
                "address": {
                "street": "Kulas Light",
                "suite": "Apt. 556",
                "city": "Gwenborough",
                "zipcode": "92998-3874",
                "geo": {
                "lat": "-37.3159",
                "lng": "81.1496"
                }
                },
                "phone": "{$normalizedPhoneNumber}",
                "website": "hildegard.org",
                "company": {
                "name": "Romaguera-Crona",
                "catchPhrase": "Multi-layered client-server neural-net",
                "bs": "harness real-time e-markets"
                }}
                JSON, true);
    }
}