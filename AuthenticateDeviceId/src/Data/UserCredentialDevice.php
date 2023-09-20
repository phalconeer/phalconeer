<?php
namespace Phalconeer\AuthenticateDeviceId\Data;

use Phalconeer\Data;
use Phalconeer\Dto;
use Phalconeer\AuthenticateDeviceId as This;

class UserCredentialDevice extends Dto\ImmutableDto implements Dto\ArrayObjectExporterInterface
{
    use Dto\Trait\ArrayObjectExporter,
        Data\Trait\Data\ParseTypes,
        Data\Trait\Data\AutoGetter;

    protected static array $exportTransformers = [
        Dto\Helper\TraitsHelper::EXPORT_METHOD_TO_ARRAY_OBJECT
    ];

    protected int $id;

    protected string $deviceId;

    protected int $userId;

    protected string $status;

    public function changeDeviceId(string $deviceId = null) : self
    {
        return $this->setValueByKey('deviceId', $deviceId);
    }

    public function isValid() : bool
    {
        return $this->status === This\Helper\AuthenticateDeviceIdHelper::STATUS_ALLOWED;
    }
}