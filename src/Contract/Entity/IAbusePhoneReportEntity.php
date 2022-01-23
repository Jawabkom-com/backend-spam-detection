<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Contract\Entity;

interface IAbusePhoneReportEntity
{
    public function getReporterId():string;
    public function getAbuseType():string;

    public function setReporterId(string $reporterId):void;
    public function setAbuseType(string $type):void;
}
