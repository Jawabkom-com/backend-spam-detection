<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Service;

use Jawabkom\Backend\Module\Spam\Detection\Contract\Service\IAddPhoneSpamScoreService;
use SleekDB\Store;

class AddPhoneSpamScoreService implements IAddPhoneSpamScoreService
{

    private array $inputs = [];
    private array $outputs = [];

    public function __construct()
    {
        if(!is_dir(__DIR__ .'/../../tests/tmp')) {
            mkdir(__DIR__ .'/../../tests/tmp', 0777, true);
        }
    }

    public function process(): static
    {
        $store = new Store('phone_spam_scores', __DIR__ .'/../../tests/tmp', ['timeout' => false]);
        $record = $store->insert($this->inputs);
        $this->outputs['result'] = $record;
        return $this;
    }

    public function input(string $key, mixed $value): static
    {
        $this->inputs[$key] = $value;
        return $this;
    }

    public function inputs(array $inputs): static
    {
        $this->inputs = $inputs;
        return $this;
    }

    public function output(string $key): mixed
    {
        return $this->outputs[$key];
    }

    public function outputs(): array
    {
        return $this->outputs;
    }
}