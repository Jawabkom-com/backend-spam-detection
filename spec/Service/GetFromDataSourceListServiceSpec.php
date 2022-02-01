<?php

namespace spec\Jawabkom\Backend\Module\Spam\Detection\Service;

use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\ISpamPhoneScoreEntity;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Repository\ISpamPhoneScoreRepository;
use Jawabkom\Backend\Module\Spam\Detection\DataSourceRegistry;
use Jawabkom\Backend\Module\Spam\Detection\Service\GetFromDataSourceListService;
use Jawabkom\Backend\Module\Spam\Detection\Test\Classes\Entity\DummySpamPhoneScoreEntity;
use Jawabkom\Backend\Module\Spam\Detection\Test\Classes\Repository\DummySpamPhoneScoreRepository;
use Jawabkom\Standard\Contract\IDependencyInjector;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use WabLab\DI\DI;

class GetFromDataSourceListServiceSpec extends ObjectBehavior
{

}
