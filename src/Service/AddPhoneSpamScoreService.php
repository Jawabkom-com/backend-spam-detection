<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Service;

use Jawabkom\Backend\Module\Spam\Detection\Contract\Service\IAddPhoneSpamScoreService;
use Jawabkom\Standard\Abstract\AbstractService;
use SleekDB\Store;

class AddPhoneSpamScoreService implements IAddPhoneSpamScoreService
{
    private $input = [];
    private $output = [];

    public function process(): static
    {
        $store = new Store('phone_spam_scores', __DIR__ .'/../../tests/tmp', ['timeout' => false]);
        $record = $store->insert($this->input);
        $this->output['result'] = $record;
        return $this;
    }

    public function input(string $key, mixed $value): static
    {
        $this->input[$key] = $value;
        return $this;
    }

    public function inputs(array $inputs): static
    {
        $this->input = array_merge($this->input, $inputs);
        return $this;
    }

    public function output(string $key): mixed
    {
        return $this->output[$key] ?? null;
    }

    public function outputs(): array
    {
        return $this->output;
    }
}