<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Contract\Entity;

interface ISpamPhoneScoreEntity
{
    public function getPhone():?string;
    public function getSource():?string;
    public function getCountryCode():?string;
    public function getTags():?array;
    public function getScore():?float;
    public function getCreatedDateTime():?\DateTime;
    public function getUpdatedDateTime():?\DateTime;

    public function setPhone(?string $phone):void;
    public function setSource(?string $source):void;
    public function setCountryCode(?string $countryCode):void;
    public function setTags(?array $tags):void;
    public function setScore(?float $score):void;
    public function setCreatedDateTime(?\DateTime $dateTime):void;
    public function setUpdatedDateTime(?\DateTime $dateTime):void;
}
