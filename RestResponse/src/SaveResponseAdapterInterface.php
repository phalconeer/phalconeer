<?php
namespace Phalconeer\Module\RestResponse;

use Phalcon\Http\ResponseInterface;

interface SaveResponseAdapterInterface
{
    public function save(ResponseInterface $response);
}