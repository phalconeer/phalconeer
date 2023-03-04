<?php
namespace Phalconeer\FormValidator\Test\Form;

use Phalconeer\FormValidator\Test as This;
use Phalconeer\FormValidator;
use Phalconeer\FormValidator\Helper\FormValidatorHelper as FVH;

class PossibleValuesForm  extends FormValidator\Data\Form
{
    protected static array $fields = [
        'possibleValuesField'         => [
            FVH::CHECK_POSSIBLE_VALUES         => [
                FVH::KEY_VALUE              => [
                    1,
                    'a',
                    true,
                    -0.5
                ],
                FVH::KEY_EXCEPTION_CODE     => This\Helper\ExceptionHelper::POSSIBLE_VALUE_EXCEPTION
            ]
        ]
    ];
}