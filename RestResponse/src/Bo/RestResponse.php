<?php

namespace Phalconeer\RestResponse\Bo;

use Phalcon\Config as PhalconConfig;
use Phalcon\Mvc;
use Phalcon\Http;
use Phalconeer\RestRequest;
use Phalconeer\RestResponse as This;

class RestResponse extends Http\Response
{
    protected string $applicationName;

    protected string $charset = 'UTF-8';

    protected string $format = This\Helper\RestResponseHelper::FORMAT_JSON;

    protected This\ResourceInterface $resource;

    public function __construct(
        $content = null,
        $code = null,
        $status = null,
        protected RestRequest\Bo\RestRequest $request,
        protected Mvc\Url\UrlInterface $url,
        protected PhalconConfig\Config $config = new PhalconConfig\Config()
    )
    {
        parent::__construct($content, $code, $status);
    }

    public function setApplicationName(string $applicationName) : self
    {
        $this->applicationName = $applicationName;
        return $this;
    }

    protected function setFormat(string $contentType)
    {
        if (strpos($contentType, This\Helper\RestResponseHelper::FORMAT_JSON) !== false) {
            $this->format = This\Helper\RestResponseHelper::FORMAT_JSON;
        }
        if (strpos($contentType, This\Helper\RestResponseHelper::FORMAT_CSV) !== false) {
            $this->format = This\Helper\RestResponseHelper::FORMAT_CSV;
        }
        // Force HTML
        else if (strpos($contentType, This\Helper\RestResponseHelper::FORMAT_HTML) !== false) {
            $this->format = This\Helper\RestResponseHelper::FORMAT_HTML;
        }
        // else if (strpos($contentType, This\Helper\RestResponseHelper::FORMAT_XHTML) !== false) {
        //     $this->format = This\Helper\RestResponseHelper::FORMAT_XHTML;
        // }
        // else if (strpos($contentType, This\Helper\RestResponseHelper::FORMAT_XML) !== false) {
        //     $this->format = This\Helper\RestResponseHelper::FORMAT_XML;
        // }
        else {
            $this->format = This\Helper\RestResponseHelper::FORMAT_JSON;
        }
    }

    public function getFormat() : string
    {
        return $this->format;
    }

    public function getResource() : ?This\ResourceInterface
    {
        return $this->resource;
    }

    public function setContentType(
        string $contentType,
        $charset = null
    ) : Http\ResponseInterface
    {
        $this->setFormat($contentType);
        if (!is_null($charset)) {
            $this->charset = $charset;
        }

        return $this;
    }

    public function setSelfLink(
        This\ResourceInterface $resource,
        string $routeName,
        int $limit = null,
        int $offset = null,
        string $sort = '',
        array $queryParameters = []
    )
    {
        $resource->addLink(
            This\Helper\RestResponseHelper::LINK_SELF,
            $this->url->get(
                [
                    'for'       => $routeName,
                ],
                $this->request->getPagerQueryParams(
                    $limit,
                    $offset,
                    $sort,
                    $queryParameters
                )
            )
        );
    }

    public function setPagerLinks(
        This\ResourceInterface $resource,
        string $routeName,
        int $size,
        int $limit = null,
        int $offset = null,
        string $sort = '',
        array $queryParameters = []
    ) {
        $this->setSelfLink(
            $resource,
            $routeName,
            $limit,
            $offset,
            $sort,
            $queryParameters
        );
        if ($offset >= $limit) {
            $resource->addLink(
                This\Helper\RestResponseHelper::LINK_FIRST,
                $this->url->get(
                    [
                        'for'       => $routeName,
                    ],
                    $this->request->getPagerQueryParams(
                        $limit,
                        0,
                        $sort,
                        $queryParameters
                    )
                )
            );
        }
        if ($offset > $limit) {
            $resource->addLink(
                This\Helper\RestResponseHelper::LINK_PREV,
                $this->url->get(
                    [
                        'for'       => $routeName,
                    ],
                    $this->request->getPagerQueryParams(
                        $limit,
                        $offset - $limit,
                        $sort,
                        $queryParameters
                    )
                )
            );
        }
        if ($offset + $limit < $size - $limit) {
            $resource->addLink(
                This\Helper\RestResponseHelper::LINK_NEXT,
                $this->url->get(
                    [
                        'for'       => $routeName,
                    ],
                    $this->request->getPagerQueryParams(
                        $limit,
                        $offset + $limit,
                        $sort,
                        $queryParameters
                    )
                )
            );
        }
        if ($offset + $limit < $size) {
            $correction = ($size % $limit === 0) ? 1 : 0;
            $resource->addLink(
                This\Helper\RestResponseHelper::LINK_FIRST,
                $this->url->get(
                    [
                        'for'       => $routeName,
                    ],
                    $this->request->getPagerQueryParams(
                        $limit,
                        floor($size / $limit - $correction) * $limit,
                        $sort,
                        $queryParameters
                    )
                )
            );
        }
    }

    protected function forceContentType(string $contentType) : Http\ResponseInterface
    {
        parent::setContentType($contentType);
        return $this;
    }

    protected function forceJsonContent(This\ResourceInterface $resource)
    {
        parent::setContent($resource->convertTo($this->format));
        $this->forceContentType(
                $this->config->get('contentType', 'application/vnd') . 
                (($this->applicationName) ? '.' . $this->applicationName : '') .
                '+' . $this->format,
            $this->charset);
        return $this;
    }

    protected function getCsvFileName() : string
    {
        return implode('', [
            $this->config->get('exportFileBaseName', 'export-'),
            (new \DateTime())->format('ymd-His'),
            '.csv'
        ]);
    }

    protected function forceCsvContent(This\ResourceInterface $resource)
    {
        parent::setContent($resource->convertTo($this->format));
        $this->setHeader(
            'Content-Disposition',
            'attachment; filename="' . $this->getCsvFileName() . '"');
        $this->forceContentType('file/csv');
        return $this;
    }

    public function setResource(This\ResourceInterface $resource)
    {
        $this->resource = $resource;
        if ($this->format === This\Helper\RestResponseHelper::FORMAT_JSON) {
            return $this->forceJsonContent($resource);
        }
        if ($this->format === This\Helper\RestResponseHelper::FORMAT_CSV) {
            return $this->forceCsvContent($resource);
        }
    }

    public function setContent(string $content = null) : Http\ResponseInterface
    {
        if ($this->format === This\Helper\RestResponseHelper::FORMAT_JSON) {
            return $this;
        }
        if ($this->format === This\Helper\RestResponseHelper::FORMAT_CSV) {
            return $this;
        }
        return parent::setContent($content);
    }
}
