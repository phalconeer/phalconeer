<?php
namespace Phalconeer\Impression;

use Phalconeer\Dao;

interface ImpressionBoInterface
{
    public function addAdapter(Dao\DaoReadAndWriteInterface $adapter);

    public function addTag(string $tag);

    public function mergeData(array $data = []);

    public function save();
}