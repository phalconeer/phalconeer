<?php
namespace Phalconeer\FormValidator\Test\Form;

use Phalconeer\FormValidator\Test as This;
use Phalconeer\FormValidator;
use Phalconeer\FormValidator\Helper\FormValidatorHelper as FVH;

class LocalStrictModeEnabledForm extends FormValidator\Data\Form
{
    protected static array $fields = [
        'boolField'         => [
            FVH::CHECK_TYPE        => [
                FVH::KEY_VALUE              => FVH::TYPE_BOOL,
                FVH::KEY_EXCEPTION_CODE     => This\Helper\ExceptionHelper::BOOL_EXCEPTION,
                FVH::KEY_STRICT             => true,
            ]
        ]
    ];
}