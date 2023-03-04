<?php
namespace Phalconeer\FormValidator\Test\Form;

use Phalconeer\FormValidator\Test as This;
use Phalconeer\FormValidator;
use Phalconeer\FormValidator\Helper\FormValidatorHelper as FVH;

class MinValueForm  extends FormValidator\Data\Form
{
    protected static array $fields = [
        'minValueField'         => [
            FVH::CHECK_MIN_VALUE         => [
                FVH::KEY_VALUE              => 4,
                FVH::KEY_EXCEPTION_CODE     => This\Helper\ExceptionHelper::MIN_VALUE_EXCEPTION
            ]
        ]
    ];
}