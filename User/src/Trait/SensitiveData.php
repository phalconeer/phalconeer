<?php
namespace Phalconeer\User\Trait;

use Phalconeer\User as This;

trait SensitiveData
{
    public function exportSensitiveData(\ArrayObject $data) : \ArrayObject
    {
        $parameters = new \ArrayObject([
            'sensitiveParameters'     => $this->getSensitiveProperties()
        ]);

        return This\Transformer\SensitiveData::sensitiveData(
            $data,
            $parameters
        );
    }
}