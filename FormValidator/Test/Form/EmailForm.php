<?php
namespace Phalconeer\FormValidator\Test\Form;

use Phalconeer\FormValidator\Test as This;
use Phalconeer\FormValidator;
use Phalconeer\FormValidator\Helper\FormValidatorHelper as FVH;

class EmailForm  extends FormValidator\Data\Form
{
    protected static array $fields = [
        'emailField'         => [
            FVH::CHECK_TYPE        => [
                FVH::KEY_VALUE              => FVH::TYPE_EMAIL,
                FVH::KEY_EXCEPTION_CODE     => This\Helper\ExceptionHelper::EMAIL_EXCEPTION
            ]
        ]
    ];
}