<?php
namespace Phalconeer\Exception\Export;

use Phalconeer\Data;
use Phalconeer\Dto;
use Phalconeer\Exception as This;

class Exception extends Dto\ImmutableDto
{
    use Dto\Trait\ArrayLoader,
        Dto\Trait\ArrayObjectExporter,
        Data\Trait\Data\ParseTypes,
        Data\Trait\Data\AutoGetter;
    
    protected string $code;

    protected string $file;

    protected string $id;

    protected int $line;

    protected $message;

    protected self $previous;

    protected int $statusCode;

    protected This\Export\ExceptionTraceCollection $trace;

    protected string $type;

    public function setPrevious(self $previous) : self
    {
        return $this->setKeyValue('previous', $previous);
    }

    public function getPrimaryKey(): array
    {
        return ['id'];
    }

    public static function fromException(\Exception $exception) : self
    {
        return self::fromArray([
            'id'                => This\Helper\ReadableIdHelper::getId(),
            'code'              => $exception->getCode(),
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
                return This\Export\ExceptionTrace::fromArray($traceItem);
            }, $exception->getTrace()))
        ]);
    }
}