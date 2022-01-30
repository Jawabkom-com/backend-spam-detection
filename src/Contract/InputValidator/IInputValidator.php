<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Contract\InputValidator;

interface IInputValidator
{
    public function validate(array $inputs);
}
