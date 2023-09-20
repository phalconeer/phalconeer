<?php
namespace Phalconeer\User\Data;

use Phalconeer\Dto;
use Phalconeer\User as This;

class UserSensitiveData extends This\Data\User
{
    use This\Trait\SensitiveData;

    protected static array $exportTransformers = [
        Dto\Helper\TraitsHelper::EXPORT_METHOD_TO_ARRAY_OBJECT,
        This\Transformer\SensitiveData::TRAIT_METHOD
    ];

    public static function fromUser(This\Data\User $user) : self
    {
        $data = $user->exportWithTransformers([
            Dto\Helper\TraitsHelper::EXPORT_METHOD_TO_ARRAY_OBJECT
        ]);

        return new self($data);
    }
}