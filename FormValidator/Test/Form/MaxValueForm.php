<?php
namespace Phalconeer\FormValidator\Test\Form;

use Phalconeer\FormValidator\Test as This;
use Phalconeer\FormValidator;
use Phalconeer\FormValidator\Helper\FormValidatorHelper as FVH;

class MaxValueForm  extends FormValidator\Data\Form
{
    protected static array $fields = [
        'maxValueField'         => [
            FVH::CHECK_MAX_VALUE         => [
                FVH::KEY_VALUE              => 4,
                FVH::KEY_EXCEPTION_CODE     => This\Helper\ExceptionHelper::MAX_VALUE_EXCEPTION
            ]
        ]
    ];
}