<?php
namespace Phalconeer\AuthMethod\Data;

use Phalconeer\AuthMethod as This;
use Phalconeer\Data;
use Phalconeer\Dto;
use Phalconeer\LiveSession;

class AuthenticationRequest extends Dto\ImmutableDto
{
    use Dto\Trait\ArrayLoader,
        Data\Trait\ParseTypes,
        Data\Trait\AutoGetter;

    protected static array $loadTransformers = [
        This\Transformer\AddRequestId::class,
        This\Transformer\AddRequestTime::class,
    ];

    protected ?string $liveSessionType = LiveSession\Helper\LiveSessionHelper::LIVE_SESSION_TYPE_DEFAULT;

    protected string $method;

    protected ?string $password;

    protected ?string $requestId;

    protected \DateTime $requestTime;

    protected ?string $sessionId;

    protected ?int $userId;

    protected ?string $username;

    public function setLiveSessionType(string $liveSessionType) : self
    {
        return $this->setValueByKey('liveSessionType', $liveSessionType);
    }
}