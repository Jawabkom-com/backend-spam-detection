<?php

namespace spec\Jawabkom\Backend\Module\Spam\Detection\Service;

use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\ISpamPhoneScoreEntity;
use Jawabkom\Backend\Module\Spam\Detection\Contract\Repository\ISpamPhoneScoreRepository;
use Jawabkom\Backend\Module\Spam\Detection\Exception\RequiredPhoneException;
use Jawabkom\Backend\Module\Spam\Detection\Exception\RequiredSourceException;
use Jawabkom\Backend\Module\Spam\Detection\Service\AddUpdatePhoneSpamScoreService;
use Jawabkom\Backend\Module\Spam\Detection\Test\Classes\Entity\DummySpamPhoneScoreEntity;
use Jawabkom\Backend\Module\Spam\Detection\Test\Classes\Repository\DummySpamPhoneScoreRepository;
use Jawabkom\Standard\Contract\IDependencyInjector;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use WabLab\DI\DI;

class AddUpdatePhoneSpamScoreServiceSpec extends ObjectBehavior
{
    public function let(IDependencyInjector $di)
    {
        $wablabDi = new DI();
        $wablabDi->register(ISpamPhoneScoreEntity::class, DummySpamPhoneScoreEntity::class);
        $wablabDi->register(ISpamPhoneScoreRepository::class, DummySpamPhoneScoreRepository::class);

        $di->make(Argument::any(), Argument::any())->will(function ($args) use($wablabDi) {
            $alias = $args[0];
            $aliasArgs = $args[1] ?? [];
            return $wablabDi->make($alias, $aliasArgs);
        });
        DummySpamPhoneScoreRepository::$DB = [];
        $this->beConstructedWith($di->getWrappedObject());
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(AddUpdatePhoneSpamScoreService::class);
    }

    function it_should_create_phone_spam_record_if_all_inputs_provided()
    {
        $result = $this->inputs([
            'phone' => '+970599189357',
            'score' => 10,
            'source' => 'test',
            'country_code' => 'PS',
            'tags' => []
        ])->process()->output('result');

        $result->shouldBeAnInstanceOf(ISpamPhoneScoreEntity::class);
    }

    function it_should_throw_exception_if_data_not_provided()
    {
        $this->inputs([
            'phone' => '',
            'score' => 10,
            'source' => 'test',
            'country_code' => 'PS',
            'tags' => []
        ]);

        $this->shouldThrow(RequiredPhoneException::class)->duringProcess();
    }

    public function it_should_throw_required_source_exception_if_not_source_provided()
    {
        $this->inputs([
            'phone' => '+970599189357',
            'score' => 10,
            'country_code' => 'PS',
            'tags' => []
        ]);

        $this->shouldThrow(RequiredSourceException::class)->duringProcess();
    }

    public function it_should_trim_all_inputs_if_provided_with_spaces()
    {
        $result = $this
            ->inputs([
                'phone' => '   +970599189357   ',
                'score' => '   10  ',
                'source' => ' test   ',
                'country_code' => '   PS    ',
                'tags' => [' personal   ']
            ])
            ->process()
            ->output('result');

        $result->shouldBeAnInstanceOf(ISpamPhoneScoreEntity::class);
        $result->getPhone()->shouldBe('+970599189357');
        $result->getSource()->shouldBe('test');
        $result->getCountryCode()->shouldBe('PS');
        $result->getTags()->offsetGet(0)->shouldBe('personal');
    }

    public function it_should_convert_score_to_float_if_string_provided()
    {
        $result = $this
            ->inputs([
                'phone' => '   +970599189357   ',
                'score' => ' 10 ',
                'source' => ' test   ',
                'country_code' => '   PS    ',
                'tags' => [' personal   ']
            ])
            ->process()
            ->output('result');

        $result->shouldBeAnInstanceOf(ISpamPhoneScoreEntity::class);
        $result->getScore()->shouldBeFloat();
    }

    public function it_should_autofill_the_country_code_if_the_phone_number_is_normalized()
    {
        $this
            ->inputs([
                'phone' => '+970599189357',
                'score' => '10',
                'source' => 'data_source',
                'country_code' => '',
                'tags' => ['personal']
            ])
            ->process()
            ->output('result')
            ->getCountryCode()->shouldBe('PS');
    }

    public function it_should_normalize_phone_number_if_country_code_provided_and_phone_number_is_not_formatted()
    {
        $this
            ->inputs([
                'phone' => '0599189357',
                'score' => '10',
                'source' => 'data_source',
                'country_code' => 'PS',
                'tags' => ['personal']
            ])
            ->process()
            ->output('result')
            ->getPhone()->shouldBe('+970599189357');
    }
}
