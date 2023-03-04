<?php
namespace Phalconeer\FormValidator\Test\Form;

use Phalconeer\FormValidator\Test as This;
use Phalconeer\FormValidator;
use Phalconeer\FormValidator\Helper\FormValidatorHelper as FVH;

class DateForm  extends FormValidator\Data\Form
{
    protected static array $fields = [
        'dateField'         => [
            FVH::CHECK_TYPE        => [
                FVH::KEY_VALUE              => FVH::TYPE_DATE,
                FVH::KEY_EXCEPTION_CODE     => This\Helper\ExceptionHelper::DATE_EXCEPTION
            ]
        ]
    ];
}