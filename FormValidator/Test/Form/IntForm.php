<?php
namespace Phalconeer\FormValidator\Test\Form;

use Phalconeer\FormValidator\Test as This;
use Phalconeer\FormValidator;
use Phalconeer\FormValidator\Helper\FormValidatorHelper as FVH;

class IntForm  extends FormValidator\Data\Form
{
    protected static array $fields = [
        'intField'         => [
            FVH::CHECK_TYPE        => [
                FVH::KEY_VALUE              => FVH::TYPE_INT,
                FVH::KEY_EXCEPTION_CODE     => This\Helper\ExceptionHelper::INT_EXCEPTION
            ]
        ]
    ];
}