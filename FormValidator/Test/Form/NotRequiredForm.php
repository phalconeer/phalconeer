<?php
namespace Phalconeer\FormValidator\Test\Form;

use Phalconeer\FormValidator;
use Phalconeer\FormValidator\Helper\FormValidatorHelper as FVH;

class NotRequiredForm  extends FormValidator\Data\Form
{
    protected static array $fields = [
        'notRequiredField'         => [
            FVH::CHECK_REQUIRED         => [
                FVH::KEY_VALUE              => false,
            ]
        ]
    ];
}