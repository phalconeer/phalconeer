<?php
namespace Phalconeer\User\Data;

use Phalconeer\Dto;
use Phalconeer\User as This;

class UserSensitiveData extends This\Data\User
{
    use This\Trait\SensitiveData;

    protected static array $exportTransformers = [
        Dto\Transformer\ArrayObjectExporter::TRAIT_METHOD,
        This\Transformer\SensitiveData::TRAIT_METHOD
    ];

    public static function fromUser(This\Data\User $user) : self
    {
        $data = $user->exportWithTransformers([
            Dto\Transformer\ArrayObjectExporter::TRAIT_METHOD
        ]);

        return new self($data);
    }
}