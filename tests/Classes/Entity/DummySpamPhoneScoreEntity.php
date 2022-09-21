<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Test\Classes\Entity;

use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\ISpamPhoneScoreEntity;

class DummySpamPhoneScoreEntity implements ISpamPhoneScoreEntity
{

    private $phone;
    private $source;
    private $country_code;
    private $tags = [];
    private $score;
    private $created_at;
    private $updated_at;
    private $meta = [];

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function getCountryCode(): string
    {
        return $this->country_code;
    }

    public function getTags(): ?array
    {
        return $this->tags;
    }

    public function getScore(): float
    {
        return $this->score;
    }

    public function getCreatedDateTime(): ?\DateTime
    {
        return $this->created_at;
    }

    public function getUpdatedDateTime(): ?\DateTime
    {
        return $this->updated_at;
    }

    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    public function setSource(?string $source): void
    {
        $this->source = $source;
    }

    public function setCountryCode(?string $countryCode): void
    {
        $this->country_code = $countryCode;
    }

    public function setTags(?array $tags): void
    {
        $this->tags = $tags;
    }

    public function setScore(?float $score): void
    {
        $this->score = $score;
    }

    public function setCreatedDateTime(?\DateTime $dateTime): void
    {
        $this->created_at = $dateTime;
    }

    public function setUpdatedDateTime(?\DateTime $dateTime): void
    {
        $this->updated_at = $dateTime;
    }

    public function getMeta(): ?array
    {
        return $this->meta;
    }

    public function setMeta(?array $meta): void
    {
        $this->meta = $meta;
    }
}