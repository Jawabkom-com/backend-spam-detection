<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Contract\Entity;

interface ISearchRequestEntity
{
    public function setHash(string $hash);
    public function getHash():string;

    public function setRequestSearchFilters(array $request);
    public function getRequestSearchFilters():array;

    public function setRequestSearchResults(array $result);
    public function getRequestSearchResults():array;

    public function setRequestDateTime(\DateTime $dateTime);
    public function getRequestDateTime():\DateTime;

    public function setResultAliasSource(string $alias);
    public function getResultAliasSource():string;

    public function setIsFromCache(bool $isFromCache);
    public function getIsFromCache(): bool;

    public function setOtherParams(array $params);
    public function getOtherParams():array;

    public function setMatchesCount(int $count);
    public function getMatchesCount():int;

    public function setStatus(string $status);
    public function getStatus():string;

    public function addError(string $message);

    /**
     * @return string[]
     */
    public function getErrors():iterable;
}