<?php
namespace Phalconeer\Data\Trait\Data;

trait AutoGetter
{
    public function __call($functionName, $arguments)
    {
        if (property_exists($this, $functionName)) {
            $arguments = [
                $functionName,
                array_key_exists(0, $arguments) ? $arguments[0] : true,
                array_key_exists(1, $arguments) ? $arguments[1] : true,
            ];
            
            return call_user_func_array(
                [$this, 'getValue'],
                $arguments
            );
        }

        if (substr($functionName, 0, 3) === 'set'
            && array_key_exists(0, $arguments)) {
            $proprety = lcfirst(substr($functionName, 3));
            if (property_exists($this, $proprety)) {
                return $this->setValueByKey($proprety, $arguments[0]);
            }
        }
    }
}