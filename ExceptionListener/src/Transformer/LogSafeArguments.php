<?php
namespace Phalconeer\ExceptionListener\Transformer;

use Phalconeer\Data;
use Phalconeer\Dto;

class LogSafeArguments implements Dto\TransformerStaticInterface
{
    public static function transformStatic(
        \ArrayObject | Data\CommonInterface $source,
        Data\CommonInterface $baseObject = null,
        \ArrayObject $parameters = null
    )
    {
        $source = Dto\Transformer\ArrayObjectExporter::normalizeArrayObject($source);

        if ($source->offsetExists('arguments')
            && is_array($arguments = $source->offsetGet('arguments'))) {
            foreach ($arguments as $index => $argument) {
                $convertedArgument = null;
                if ($argument instanceof Dto\ArrayObjectExporterInterface) {
                    $convertedArgument = json_encode($argument->toArrayObject()->getArrayCopy());
                }
                if (is_array($argument)) {
                    $convertedArgument = json_encode($argument);
                }
                if ($convertedArgument) {
                    $arguments[$index] = $convertedArgument;
                }
            }

            $source->offsetSet('arguments', $arguments);
        }

        return $source;
    }
}