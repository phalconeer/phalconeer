<?php
namespace Phalconeer\FormValidator\Test\Form;

use Phalconeer\FormValidator\Test as This;
use Phalconeer\FormValidator;
use Phalconeer\FormValidator\Helper\FormValidatorHelper as FVH;

class MaxLengthForm  extends FormValidator\Data\Form
{
    protected static array $fields = [
        'maxLengthField'         => [
            FVH::CHECK_MAX_LENGTH         => [
                FVH::KEY_VALUE              => 7,
                FVH::KEY_EXCEPTION_CODE     => This\Helper\ExceptionHelper::MAX_LENGTH_EXCEPTION
            ]
        ]
    ];
}