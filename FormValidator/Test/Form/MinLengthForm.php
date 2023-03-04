<?php
namespace Phalconeer\FormValidator\Test\Form;

use Phalconeer\FormValidator\Test as This;
use Phalconeer\FormValidator;
use Phalconeer\FormValidator\Helper\FormValidatorHelper as FVH;

class MinLengthForm  extends FormValidator\Data\Form
{
    protected static array $fields = [
        'minLengthField'         => [
            FVH::CHECK_MIN_LENGTH         => [
                FVH::KEY_VALUE              => 4,
                FVH::KEY_EXCEPTION_CODE     => This\Helper\ExceptionHelper::MIN_LENGTH_EXCEPTION
            ]
        ]
    ];
}