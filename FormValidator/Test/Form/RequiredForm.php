<?php
namespace Phalconeer\FormValidator\Test\Form;

use Phalconeer\FormValidator\Test as This;
use Phalconeer\FormValidator;
use Phalconeer\FormValidator\Helper\FormValidatorHelper as FVH;

class RequiredForm  extends FormValidator\Data\Form
{
    protected static array $fields = [
        'requiredField'         => [
            FVH::CHECK_REQUIRED         => [
                FVH::KEY_VALUE              => true,
                FVH::KEY_EXCEPTION_CODE     => This\Helper\ExceptionHelper::REQUIRED_EXCEPTION
            ]
        ]
    ];
}