<?php
namespace Phalconeer\FormValidator\Test\Form;

use Phalconeer\FormValidator\Test as This;
use Phalconeer\FormValidator\Helper\FormValidatorHelper as FVH;

class ExtendedMaxValueForm extends This\Form\MaxValueForm
{
    protected static array $fields = [
        'maxValueField'         => [
            FVH::CHECK_MAX_VALUE         => [
                FVH::KEY_VALUE              => 7,
                FVH::KEY_EXCEPTION_CODE     => This\Helper\ExceptionHelper::MAX_VALUE_EXCEPTION // This needs to be repeated even if it matches the aprent value
            ],
            FVH::CHECK_MIN_VALUE    => [
                FVH::KEY_VALUE          => 5,
                FVH::KEY_EXCEPTION_CODE     => This\Helper\ExceptionHelper::MIN_VALUE_EXCEPTION
            ]
        ]
    ];
}