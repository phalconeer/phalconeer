<?php
namespace Phalconeer\RestResponse\Data\Traits;

use Phalconeer\Exception;
use Phalconeer\Dto;
use Phalconeer\RestResponse as This;

Trait Resource
{
    use Dto\Traits\ArrayExporter;

    protected \ArrayObject $meta;

    protected \ArrayObject $links;

    public function initializeData(\ArrayObject $inputObject) : \ArrayObject 
    {
        $inputObject = parent::initializeData($inputObject);
        if (!$inputObject->offsetExists('meta')) {
            $inputObject->offsetSet('meta', new \ArrayObject());
        }
        if (!$inputObject->offsetExists('links')) {
            $inputObject->offsetSet('links', new \ArrayObject());
        }
        return $inputObject;
    }

    public function getResourceType() : string
    {
        $className = str_replace('Resource', '', get_called_class());
        return strtolower(preg_replace('/([A-Z])/', '-\\1', lcfirst(substr($className, strrpos($className, '\\') + 1))));
    }

    public function convertTo(string $format) : string
    {
        switch ($format) {
            case This\Helper\RestResponseHelper::FORMAT_JSON:
                return $this->toJSON();
            case This\Helper\RestResponseHelper::FORMAT_CSV:
                return $this->toCSV();
            case This\Helper\RestResponseHelper::FORMAT_XML:
            case This\Helper\RestResponseHelper::FORMAT_XHTML:
            case This\Helper\RestResponseHelper::FORMAT_HTML:
            default:
                throw new This\Exception\UnsupportedFormatException('Format (' . $format . ') not supported!');
        }
    }

    public function meta() : \ArrayObject
    {
        if (is_null($this->meta)) {
            $this->meta = new \ArrayObject();
        }

        return $this->meta;
    }

    /**
     * It is possible to not have the property prepopulated when the resource is a collection
     */
    public function links() : \ArrayObject
    {
        if (is_null($this->links)) {
            $this->links = new \ArrayObject();
        }

        return $this->links;
    }

    public function addMeta($key, $value) : This\ResourceInterface
    {
        $meta = $this->meta()->getArrayCopy(false);
        $meta[$key] = $value;
        $this->meta = new \ArrayObject($meta);
        return $this;
    }

    public function addLink($key, $value) : This\ResourceInterface
    {
        $links = $this->links()->getArrayCopy(false);
        $links[$key] = $value;
        $this->links = new \ArrayObject($links);
        return $this;
    }

    protected function toJSONApiError()
    {
        return json_encode([
            'errors'    => [
                $this->export()
            ]
        ]);
    }

    protected function collectionToJSONApi() : array
    {
        $resourceType = $this->getResourceType();
        $response = [
            'data'      => []
        ];
        $iterator = $this->getIterator();
        while ($iterator->valid()) {
            $current = $iterator->current();
            $reponse['data'][] = [
                'type'          => $resourceType,
                'id'            => implode('-', $current->getPrimaryKeyValue()),
                'attributes'    => $current->export()
            ];
            $iterator->next();
        }
        if ($this->meta && $this->meta->count() > 0) {
            $response['meta'] = $this->meta->getArrayCopy();
        }
        if ($this->links && $this->links->count() > 0) {
            $response['links'] = $this->links->getArrayCopy();
        }
        return $response;
    }

    protected function dataToJSONApi() : array
    {
        $data = $this->export();
        unset($data['links']);
        unset($data['meta']);

        return [
            'data'    => [
                'type'  => $this->getResourceType(),
                'id'    => implode('-', $this->getPrimaryKeyValue()),
                'attributes'    => $data
            ]
        ];
    }

    public function toArray() : array
    {
        return ($this instanceof \ArrayAccess)
            ? $this->collectionToJSONApi()
            : $this->dataToJSONApi();
    }

    public function toJSON() : string
    {
        if ($this instanceof Exception\ExceptionInterface) {
            return $this->toJSONApiError();
        }

        return json_encode($this->toArray());
    }

    protected function convertCsvLine(
        array $line,
        string $separator = "\t",
        $lineEnd = PHP_EOL
    ) : string
    {
        return implode($separator, array_map(
            function ($value) use ($separator) {
                if (empty($value)) {
                    return '';
                }
                strtr(
                    $value,
                    [
                        '"'         => '""',
                        "\r"        => '[[NEWLINE]]',
                        "\n"        => '[[NEWLINE]]',
                        "\r\n"      => '[[NEWLINE]]',
                        $separator  => '[[SEPARATOR]]'
                    ]
                );
                if (strpos($value, $separator) !== false
                    || !is_numeric($value)) {
                    $value = '"' . $value . '"';
                }
                return $value;
            },
            $line
        )) . $lineEnd;
    }

    public function toCSV(string $separator = "\t", $lineEnd = PHP_EOL) : string
    {
        if ($this instanceof Exception\ExceptionInterface) {
            return $this->toJSONApiError();
        }

        $response = '';
        $data = ($this instanceof \ArrayAccess)
            ? $this->export()
            : [$this->export()];
        $response .= implode($separator, array_keys($data[0])) . $lineEnd;

        foreach ($data as $current) {
            unset($current['links']);
            unset($current['meta']);
            $response .= $this->convertCsvLine($current, $separator, $lineEnd);
        }
        return $response;
    }
}
