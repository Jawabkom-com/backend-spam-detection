<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Contract\InputValidator;

use Jawabkom\Backend\Module\Spam\Detection\Contract\Entity\ISpamPhoneScoreEntity;

interface IInputValidator
{
    public function validate(array $inputs):bool;
}
