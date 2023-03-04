<?php
namespace Phalconeer\FormValidator\Test\Form;

use Phalconeer\FormValidator\Test as This;
use Phalconeer\FormValidator;
use Phalconeer\FormValidator\Helper\FormValidatorHelper as FVH;

class RegexForm extends FormValidator\Data\Form
{
    protected static array $fields = [
        'regexField'         => [
            FVH::CHECK_REGEXP         => [
                FVH::KEY_VALUE              => '/^[a-e]*$/',
                FVH::KEY_EXCEPTION_CODE     => This\Helper\ExceptionHelper::REGEX_EXCEPTION
            ]
        ]
    ];
}