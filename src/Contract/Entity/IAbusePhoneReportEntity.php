<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Contract\Entity;

interface IAbusePhoneReportEntity
{
    public function getReporterId():string;
    public function getAbuseType():string;
    public function getPhone():string;
    public function getPhoneCountryCode():string;
    public function getTags():array;

    public function setReporterId(string $reporterId):void;
    public function setAbuseType(string $type):void;
    public function setPhone(string $phone):void;
    public function setPhoneCountryCode(string $phoneCountryCode):void;
    public function setTags(array $tags):void;
}
