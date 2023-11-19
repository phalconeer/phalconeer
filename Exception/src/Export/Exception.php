<?php
namespace Phalconeer\Exception\Export;

use Phalconeer\Data;
use Phalconeer\Dto;
use Phalconeer\Exception as This;

class Exception extends Dto\ImmutableDto
{
    use Dto\Trait\ArrayLoader,
        Dto\Trait\ArrayObjectExporter,
        Data\Trait\ParseTypes,
        Data\Trait\AutoGetter;
    
    protected string $code;

    protected string $file;

    protected string $id;

    protected int $line;

    protected $message;

    protected ?This\Export\Exception $previous;

    protected int $statusCode;

    protected This\Export\ExceptionTraceCollection $trace;

    protected string $type;

    public function setPrevious(self $previous) : self
    {
        return $this->setValueByKey('previous', $previous);
    }

    public function getPrimaryKey(): array
    {
        return ['id'];
    }

    public static function fromException(\Exception $exception) : self
    {
        $exportException = self::fromArray([
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
        if (!$exception->getPrevious()) {
            return $exportException;
        }
        return $exportException->setPrevious(self::fromException($exception->getPrevious()));
    }
}