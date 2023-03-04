<?php
namespace Phalconeer\Data\Helper;

use Phalconeer\Data as This;

class CompareValueHelper
{
    public static function objectHasSameData($base, $newValue) : bool
    {
        if (method_exists($base, 'getIterator')) {
            $iterator = $base->getIterator();
            $return = true;
            while ($iterator->valid()) {
                $return = $return
                    && $newValue->offsetExists($iterator->key())
                    && static::hasSameData(
                        $iterator->current(),
                        $newValue->offsetGet($iterator->key())
                );
                $iterator->next();
            }
            if (!$return) {
                return $return;
            }
            $newIterator = $newValue->getIterator();
            while ($newIterator->valid()) {
                $return = $return
                    && $base->offsetExists($newIterator->key())
                    && static::hasSameData(
                        $newIterator->current(),
                        $base->offsetGet($newIterator->key())
                );
                $newIterator->next();
            }

            return $return;
        }
        if ($base instanceof This\ImmutableData) {
            return array_reduce(
                $base->properties(),
                function ($aggregator, $propertyName) use ($base, $newValue) {
                    return $aggregator
                        && static::hasSameData(
                            $base->{$propertyName}(),
                            $newValue->{$propertyName}()
                        );
                },
                true
            );
        }

        return $base == $newValue;
    }

    public static function hasSameData($base, $newValue) : bool
    {
        if (is_null($base)) {
            return is_null($newValue);
        }
        if (is_callable($base, false, $baseCallableName)) {
            if (!is_callable($newValue, false, $newValueCallableName)) {
                return false;
            }
            /**
             * There is no way to differentiate between two closures.
             * This will be false when two different, but names methods are supplied
             */
            return $baseCallableName === $newValueCallableName;
        }
        if (is_object($base)) {
            if (!is_object($newValue)) {
                return false;
            }
            if (get_class($base) !== get_class($newValue)) {
                return false;
            }
            return static::objectHasSameData($base, $newValue);
        }
        if (is_object($newValue)) {
            return false;
        }

        return $base === $newValue;
    }
}