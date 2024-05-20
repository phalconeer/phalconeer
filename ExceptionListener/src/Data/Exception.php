<?php
namespace Phalconeer\ExceptionListener\Data;

use Phalconeer\Data;
use Phalconeer\Dto;
use Phalconeer\ExceptionListener as This;
use Phalconeer\Id;

class Exception extends Dto\ImmutableDto implements Dto\ArrayObjectExporterInterface
{
    use This\Trait\Code,
        This\Trait\File,
        This\Trait\Id,
        This\Trait\Line,
        This\Trait\Message,
        This\Trait\Previous,
        This\Trait\RequestTime,
        This\Trait\StatusCode,
        This\Trait\Trace,
        This\Trait\Type,
        Dto\Trait\ArrayLoader,
        Dto\Trait\ArrayObjectExporter,
        Data\Trait\ParseTypes,
        Data\Trait\AutoGetter;

    protected static array $loadTransformers = [
        This\Transformer\AutoFillRequestTime::class,
    ];

    protected static array $exportTransformers = [
        Dto\Transformer\ArrayObjectExporter::TRAIT_METHOD,
    ];

    protected static array $idCache = [];

    public function getPrimaryKey(): array
    {
        return ['id'];
    }

    public static function fromException(\Exception $exception) : self
    {
        $code = $exception->getCode() ?? 0;
        if (!array_key_exists($code, static::$idCache)) {
            static::$idCache[$code] = Id\Helper\ReadableIdHelper::getId();
        }

        $exportException = self::fromArray([
            'id'                => static::$idCache[$code],
            'code'              => $code,
            'message'           => $exception->getMessage(),
            'type'              => get_class($exception),
            'file'              => $exception->getFile(),
            'line'              => $exception->getLine(),
            'server'            => This\Helper\TraceHelper::getServerAddress(),
            'trace'             => new \ArrayObject(array_map(function ($traceItem) {
                if (array_key_exists('args', $traceItem)) {
                    $traceItem['arguments'] = This\Helper\TraceHelper::flattenExceptionArguments($traceItem['args']);
                    unset($traceItem['args']);
                }
                return This\Data\ExceptionTrace::fromArray($traceItem);
            }, $exception->getTrace()))
        ]);
        if (!$exception->getPrevious()) {
            return $exportException;
        }
        return $exportException->setPrevious(self::fromException($exception->getPrevious()));
    }
}