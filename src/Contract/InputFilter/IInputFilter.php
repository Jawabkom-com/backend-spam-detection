<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Contract\InputFilter;

interface IInputFilter
{
    public function filter(array $inputs):array;
}
