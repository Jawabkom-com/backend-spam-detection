<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Service;

use Jawabkom\Backend\Module\Spam\Detection\Contract\DataSource\IDataSourceRegistry;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\ISearchRequestEntity;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Repository\ISearchRequestRepository;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Service\IGetFromDataSourceListService;
use Jawabkom\Backend\Module\Spam\Detection\Exception\RequiredPhoneException;
use Jawabkom\Backend\Module\Spam\Detection\Exception\RequiredSearchAliasException;
use Jawabkom\Backend\Module\Spam\Detection\Test\Classes\Entity\DummySearchRequestEntity;
use Jawabkom\Backend\Module\Spam\Detection\Test\Classes\Entity\DummySpamPhoneScoreEntity;
use Jawabkom\Standard\Abstract\AbstractService;
use Jawabkom\Standard\Contract\IDependencyInjector;

class GetFromDataSourceListService extends AbstractService implements IGetFromDataSourceListService
{
    private IDataSourceRegistry $dataSourceRegistry;
    private ISearchRequestRepository $searchRequestRepository;
    private ISearchRequestEntity $searchRequestEntity;

    public function __construct(IDependencyInjector $di, IDataSourceRegistry $dataSourceRegistry,
                                ISearchRequestRepository $searchRequestRepository, ISearchRequestEntity $searchRequestEntity)
    {
        parent::__construct($di);
        $this->dataSourceRegistry = $dataSourceRegistry;
        $this->searchRequestRepository = $searchRequestRepository;
        $this->searchRequestEntity = $searchRequestEntity;
    }

    /**
     * @throws RequiredPhoneException
     * @throws RequiredSearchAliasException
     */
    public function process(): static
    {
        $searchAliases = $this->getInput('searchAliases');
        $phone = $this->getInput('phone');

        $this->validateInputs($searchAliases, $phone);

        $searchGroupHash = md5(json_encode(['aliases' => $searchAliases, 'phone' => $phone]));
        //$cachedResultsByAliases = $this->getCachedResultsByAliases($searchGroupHash);

        $totalResult = [];
        $searchRequests = [];
        foreach ($searchAliases as $alias) {
            $searchRequests[] = $searchRequest =  $this->initSearchRequest($searchGroupHash, $alias);
            $registryObject = $this->dataSourceRegistry->getRegistry($alias);
            $sourceObject = $registryObject['source'];
            $data = $sourceObject->getByPhone($phone);
            $result = $registryObject['mapper']->map($data);
            $totalResult[] = $result;
            $this->updateSearchRequestSetResult($searchRequest, (array)$result);
        }
        $this->setOutput('search_requests', $searchRequests);
        $this->setOutput('result', $totalResult);
        return $this;
    }

    protected function initSearchRequest(string $hash, string $alias): ISearchRequestEntity
    {
        $entity = $this->searchRequestEntity;
        $entity->setIsFromCache(false);
        $entity->setHash($hash);
        $entity->setMatchesCount(1);
        $entity->setRequestDateTime(new \DateTime());
        $entity->setResultAliasSource($alias);
        $entity->setStatus('init');
        $this->searchRequestRepository->saveEntity($entity);
        return $entity;
    }

    protected function updateSearchRequestSetResult(ISearchRequestEntity $entity, $result)
    {
        $entity->setRequestSearchResults($result);
        $entity->setStatus('done');
        $this->searchRequestRepository->saveEntity($entity);
    }

    /**
     * @throws RequiredPhoneException
     * @throws RequiredSearchAliasException
     */
    protected function validateInputs($searchAliases, $phone)
    {
        if(empty($searchAliases)) throw new RequiredSearchAliasException('Search aliases are required, please provide one at minimum');
        if($phone == '') throw new RequiredPhoneException('Phone is required');
    }

    protected function getCachedResultsByAliases(string $searchGroupHash): array
    {
        $cachedResultsByAliases = [];
        $cachedResults = $this->searchRequestRepository->getByHash($searchGroupHash);
        if ($cachedResults) {
            foreach ($cachedResults as $cachedResult) {
                $cachedResultsByAliases[$cachedResult->getResultAliasSource()] = $cachedResult->getRequestSearchResults();
            }
        }
        return $cachedResultsByAliases;
    }

    protected function getSearchResults(bool $isFromCache, mixed $alias, string $phone, $cachedResultsByAliases): mixed
    {
        if (!$isFromCache) {
            $registryObject = $this->dataSourceRegistry->getRegistry($alias);
            $sourceObject = $registryObject['source'];
            $data = $sourceObject->getByPhone($phone);
            $results = $registryObject['mapper']->map($data);
        } else {
            $results = $cachedResultsByAliases[$alias];
        }
        return $results;
    }

}