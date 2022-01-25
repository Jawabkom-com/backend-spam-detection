<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Test;

use PHPUnit\Framework\TestCase;

class AbstractTestCase extends TestCase
{

    protected string $dbRootPath = __DIR__.'/tmp/';
    protected string $dbPath = '';
    protected array $dbConfig = ['timeout' => false];

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        `rm -rf {$this->dbRootPath}`;
        if(!is_dir($this->dbRootPath)) {
            mkdir($this->dbRootPath, 0777, true);
        }
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->dbPath = $this->dbRootPath.uniqid();
    }

}