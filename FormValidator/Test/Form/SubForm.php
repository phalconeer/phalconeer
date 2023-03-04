<?php
namespace Phalconeer\FormValidator\Test\Form;

use Phalconeer\FormValidator\Test as This;
use Phalconeer\FormValidator;
use Phalconeer\FormValidator\Helper\FormValidatorHelper as FVH;

class SubForm  extends FormValidator\Data\Form
{
    protected static array $fields = [
        'subForm'           => [
            FVH::CHECK_TYPE        => [
                FVH::KEY_VALUE              => FVH::TYPE_FORM,
                FVH::KEY_FORM               => This\Form\RequiredForm::class,
                FVH::KEY_EXCEPTION_CODE     => This\Helper\ExceptionHelper::FORM_EXCEPTION
            ]
        ],
        'subFormInt'        => [
            FVH::CHECK_TYPE        => [
                FVH::KEY_VALUE              => FVH::TYPE_FORM,
                FVH::KEY_FORM               => This\Form\IntForm::class,
                FVH::KEY_EXCEPTION_CODE     => This\Helper\ExceptionHelper::FORM_EXCEPTION
            ]
        ],
    ];
}