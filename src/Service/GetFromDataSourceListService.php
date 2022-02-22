<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Service;

use Jawabkom\Backend\Module\Spam\Detection\Contract\DataSource\IDataSourceRegistry;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\ISearchRequestEntity;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Repository\ISearchRequestRepository;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Repository\ISpamPhoneScoreRepository;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Service\IGetFromDataSourceListService;
use Jawabkom\Backend\Module\Spam\Detection\Exception\RequiredInputsException;
use Jawabkom\Backend\Module\Spam\Detection\Exception\RequiredPhoneException;
use Jawabkom\Backend\Module\Spam\Detection\Exception\RequiredSearchAliasException;
use Jawabkom\Backend\Module\Spam\Detection\Library\Phone;
use Jawabkom\Standard\Abstract\AbstractService;
use Jawabkom\Standard\Contract\IDependencyInjector;

class GetFromDataSourceListService extends AbstractService implements IGetFromDataSourceListService
{
    private IDataSourceRegistry $dataSourceRegistry;
    private ISearchRequestRepository $searchRequestRepository;

    protected array $inputAllowedKeys = ['searchAliases', 'phone', 'countryCode'];
    protected string $searchHashGroup = '';
    protected array $cachedResultsByAliases = [];
    protected string $normalizedPhoneNumber;
    protected array $searchResultsByAlias = [];
    /**
     * @var ISearchRequestEntity[]
     */
    protected array $searchRequests = [];
    protected array $errorsByAliases = [];
    protected array $mappedSearchResults = [];
    protected ISpamPhoneScoreRepository $phoneScoreRepository;

    public function __construct(IDependencyInjector $di, IDataSourceRegistry $dataSourceRegistry,
                                ISearchRequestRepository $searchRequestRepository, ISpamPhoneScoreRepository $phoneScoreRepository)
    {
        parent::__construct($di);
        $this->dataSourceRegistry = $dataSourceRegistry;
        $this->searchRequestRepository = $searchRequestRepository;
        $this->phoneScoreRepository = $phoneScoreRepository;
    }

    /**
     * @throws RequiredPhoneException
     * @throws RequiredSearchAliasException
     */
    public function process(): static
    {
        $this
            ->validateInputs()
            ->prepareNormalizedPhoneNumber()
            ->generateSearchHashGroup()
            ->fetchCachedResultsByHash()
            ->initSearchRequests()
            ->fetchSearchResults()
            ->updateSearchRequests()
            ->mapSearchResults()
            ->savePhoneScoreRecord();

        $this->setOutput('search_requests', $this->searchRequests);
        $this->setOutput('result', $this->mappedSearchResults);
        return $this;
    }

    protected function initSearchRequest(string $hash, string $alias): ISearchRequestEntity
    {
        $entity = $this->di->make(ISearchRequestEntity::class);
        $entity->setIsFromCache(false);
        $entity->setHash($hash);
        $entity->setMatchesCount(1);
        $entity->setRequestDateTime(new \DateTime());
        $entity->setResultAliasSource($alias);
        $entity->setStatus('init');
        $entity->setRequestSearchResults([]);
        $this->searchRequestRepository->saveEntity($entity);
        return $entity;
    }

    protected function updateSearchRequests():static
    {
        foreach ($this->getInput('searchAliases', []) as $alias) {
            if(isset($this->searchResultsByAlias[$alias])) {
                $this->searchRequests[$alias]->setRequestSearchResults($this->searchResultsByAlias[$alias]);
                $this->searchRequests[$alias]->setStatus('done');
            } elseif(isset($this->errorsByAliases[$alias])) {
                $this->searchRequests[$alias]->addError($this->errorsByAliases[$alias]);
                $this->searchRequests[$alias]->setStatus('error');
            } else {
                throw new \Exception('No error nor result could be found for alias '.$alias);
            }

            $this->searchRequestRepository->saveEntity($this->searchRequests[$alias]);
        }
        return $this;
    }

    protected function validateInputs():static
    {
        foreach($this->inputAllowedKeys as $key) {
            if(empty($this->getInput($key)))
                throw new RequiredInputsException($key.' are required');
        }
        return $this;
    }

    protected function initSearchRequests():static
    {
        foreach ($this->getInput('searchAliases', []) as $alias) {
            $this->searchRequests[$alias] = $this->initSearchRequest($this->searchHashGroup, $alias);
        }
        return $this;
    }

    protected function fetchSearchResults():static
    {
        foreach ($this->getInput('searchAliases', []) as $alias) {
            if(isset($this->cachedResultsByAliases[$alias])) {
                $this->searchResultsByAlias[$alias] = $this->cachedResultsByAliases[$alias];
                $this->searchRequests[$alias]->setIsFromCache(true);
                $this->searchRequestRepository->saveEntity($this->searchRequests[$alias]);
            } else {
                try {
                    $registryObject = $this->dataSourceRegistry->getRegistry($alias);
                    $sourceObject = $registryObject['source'];
                    $this->searchResultsByAlias[$alias] = $sourceObject->getByPhone($this->normalizedPhoneNumber, $this->getInput('countryCode'));
                } catch (\Throwable $exception) {
                    $this->errorsByAliases[$alias] = $exception->getMessage();
                }
            }
        }
        return $this;
    }

    protected function prepareNormalizedPhoneNumber():static
    {
        $phoneLib = new Phone();
        $possibleCountries = [];
        if($this->getInput('countryCode'))
            $possibleCountries = [$this->getInput('countryCode')];
        $parsedPhoneNumber = $phoneLib->parse($this->getInput('phone'), $possibleCountries);
        $this->normalizedPhoneNumber = $parsedPhoneNumber['phone'];
        if($parsedPhoneNumber['is_valid']) {
            $this->input('countryCode', $parsedPhoneNumber['country_code']);
            $this->input('phone', $parsedPhoneNumber['phone']);
        }
        return $this;
    }

    protected function mapSearchResults(): static
    {
        foreach ($this->searchResultsByAlias as $alias => $result) {
            $registryObject = $this->dataSourceRegistry->getRegistry($alias);
            $this->mappedSearchResults[] = $registryObject['mapper']->map($result);
        }
        return $this;
    }

    protected function savePhoneScoreRecord(): static
    {
        foreach ($this->mappedSearchResults as $entity) {
            $this->phoneScoreRepository->saveEntity($entity);
        }

        return $this;
    }

    protected function generateSearchHashGroup():static
    {
        $this->searchHashGroup = md5(json_encode(['phone' => $this->getInput('phone'), 'countryCode' => $this->getInput('countryCode')]));
        return $this;
    }

    protected function fetchCachedResultsByHash(): static
    {
        $cachedResults = $this->searchRequestRepository->getByHash($this->searchHashGroup, 'done');
        if ($cachedResults) {
            foreach ($cachedResults as $cachedResult) {
                $this->cachedResultsByAliases[$cachedResult->getResultAliasSource()] = $cachedResult->getRequestSearchResults();
            }
        }
        return $this;
    }


}