<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Test\Classes\Entity;

use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\IAbusePhoneReportEntity;

class DummyAbusePhoneReportEntity implements IAbusePhoneReportEntity
{

    private $reporterId;
    private $abuseType;
    private $phone;
    private $phoneCountryCode;
    private $tags = [];

    public function getReporterId(): string
    {
        return $this->reporterId;
    }

    public function getAbuseType(): string
    {
        return $this->abuseType;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getPhoneCountryCode(): string
    {
        return $this->phoneCountryCode;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function setReporterId(string $reporterId): void
    {
        $this->reporterId = $reporterId;
    }

    public function setAbuseType(string $type): void
    {
        $this->abuseType = $type;
    }

    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    public function setPhoneCountryCode(string $phoneCountryCode): void
    {
        $this->phoneCountryCode = $phoneCountryCode;
    }

    public function setTags(array $tags): void
    {
        $this->tags = $tags;
    }
}