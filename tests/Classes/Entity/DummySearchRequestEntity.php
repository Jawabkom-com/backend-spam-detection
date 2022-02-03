<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Test\Classes\Entity;


use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\ISearchRequestEntity;

class DummySearchRequestEntity implements ISearchRequestEntity
{

    private string $hash;
    private string|false $request_search_results;
    private \DateTime $request_date_time;
    private string $result_alias_source;
    private bool $is_from_cache;
    private string $status;

    public function setHash(string $hash)
    {
        $this->hash = $hash;
    }

    public function getHash(): string
    {
        return $this->hash;
    }
    
    public function setRequestSearchResults(array $result)
    {
        $this->request_search_results = json_encode($result);
    }

    public function getRequestSearchResults(): array
    {
        return json_decode($this->request_search_results, true);
    }

    public function setRequestDateTime(\DateTime $dateTime)
    {
        $this->request_date_time = $dateTime;
    }

    public function getRequestDateTime(): \DateTime
    {
        return $this->request_date_time;
    }

    public function setResultAliasSource(string $alias)
    {
        $this->result_alias_source = $alias;
    }

    public function getResultAliasSource(): string
    {
        return $this->result_alias_source;
    }

    public function setIsFromCache(bool $isFromCache)
    {
        $this->is_from_cache = $isFromCache;
    }

    public function getIsFromCache(): bool
    {
        return $this->is_from_cache;
    }

    public function setStatus(string $status)
    {
        $this->status = $status;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setRequestSearchFilters(array $request)
    {
        // TODO: Implement setRequestSearchFilters() method.
    }

    public function getRequestSearchFilters(): array
    {
        // TODO: Implement getRequestSearchFilters() method.
    }

    public function setOtherParams(array $params)
    {
        // TODO: Implement setOtherParams() method.
    }

    public function getOtherParams(): array
    {
        // TODO: Implement getOtherParams() method.
    }

    public function setMatchesCount(int $count)
    {
        // TODO: Implement setMatchesCount() method.
    }

    public function getMatchesCount(): int
    {
        // TODO: Implement getMatchesCount() method.
    }

    public function addError(string $message)
    {
        // TODO: Implement addError() method.
    }

    public function getErrors(): iterable
    {
        // TODO: Implement getErrors() method.
    }
}