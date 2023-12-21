<?php
namespace Phalconeer\ElasticAdapter\Dao;

use Psr;
use Phalcon\Config as PhalconConfig;
use Phalconeer\Browser;
use Phalconeer\Config;
use Phalconeer\Dao;
use Phalconeer\Data;
use Phalconeer\Dto;
use Phalconeer\ElasticAdapter as This;
use Phalconeer\ElasticAdapter\Helper\ElasticQueryBodyHelper as EQBH;
use Phalconeer\ElasticAdapter\Helper\ElasticQueryHelper as EQH;
use Phalconeer\ElasticAdapter\Helper\ElasticQueryRouteHelper as EQRH;
use Phalconeer\ElasticAdapter\Helper\ElasticResponseHelper as ERH;
use Phalconeer\Http;

class ElasticDaoBase implements Dao\DaoReadAndWriteInterface
{
    const MAX_DELETE_ONCE = 1000;

    protected Browser\BrowserInterface $browser;

    protected ?PhalconConfig\Config $config = null;
    
    protected ?Http\Data\Uri $defaultUri = null;

    /**
     * Index name. If index is date segmented, it serves as a prefix;
     */
    public string $indexName = '';

    /**
     * Format to use when generating the various date based index names.
     * Help on what format strings mean: http://php.net/manual/en/dateinterval.format.php
     */
    protected string $indexSegmentFormat = 'Y.m';

    public function __construct(
        This\Data\ElasticDaoConfiguration $daoConfiguration
    )
    {
        $this->browser = $daoConfiguration->browser();
        $this->config = $daoConfiguration->config();
        if (is_null($this->config)) {
            $this->config = Config\Helper\ConfigHelper::$dummyConfig;
        }
        $this->defaultUri = $daoConfiguration->defaultUri();
        if (is_null($this->defaultUri)) {
            $this->defaultUri = (new Http\Data\Uri())
                ->withScheme($this->config->get('protocol', 'http'))
                ->withHost($this->config->get('host', 'localhost'))
                ->withPort($this->config->get('port', 9200));
        }
        if ($this->config->offsetExists('indexName')) {
            $this->indexName = $this->config->offsetGet('indexName');
        }
    }

    /**
     * Generates a dateSegmented index name.
     */
    public function createIndexName(\DateTime $date = null) : string
    {
        if (strpos($this->indexName, '*') === false) {
            return $this->indexName;
        }

        if (is_null($date)) {
            $date = new \DateTime();
        }

        return str_replace(
            '*',
            $date->format($this->indexSegmentFormat),
            $this->indexName
        );
    }

    public function getRecord(
        array $whereConditions = [],
        bool $getSequenceInformation = true
    ) : ?\ArrayObject
    {
        $result = $this->getRecords(
            $whereConditions,
            1,
            0,
            '',
            $getSequenceInformation
        );

        return new \ArrayObject($result->offsetGet('hits')[0]);
    }

    public function getRecords(
        array $whereConditions = [],
        int $limit = 20,
        int $offset = 0,
        string $orderString = '',
        bool $getSequenceInformation = false
    ) : ?\ArrayObject
    {
        $url = $this->defaultUri->withPath($this->indexName . '/_search')
                ->withQuery(EQRH::VAR_IGNORE_UNAVAILBLE . '=true');

        $sort = EQH::buildOrderClause($orderString);
        $query = EQH::buildQuery($whereConditions);

        $bodyVariables = [
                EQBH::NODE_FROM     => $offset,
                EQBH::NODE_SIZE     => $limit,
            ];
        if (!empty($sort)) {
            $bodyVariables[EQBH::NODE_SORT] = $sort;
        }
        if (!empty($query)) {
            $bodyVariables[EQBH::NODE_QUERY] = $query;
        }
        if ($getSequenceInformation) {
            $bodyVariables[EQBH::NODE_SEQ_NO_PRIMARY_TERM] = true;
        }

        $request = Http\Data\Request::fromArray([
            'method'            => Http\Helper\HttpHelper::HTTP_METHOD_POST,
            'url'               => $url,
            'bodyVariables'     => $bodyVariables
        ]);

        $response = $this->browser->call($request);
        /**
         * @var \Phalconeer\Http\Data\Response $response
         */
// echo $limit . PHP_EOL;
// echo \Phalconeer\Dev\TVarDumper::dump($request);
// echo \Phalconeer\Dev\TVarDumper::dump($response);

        return new \ArrayObject($response->bodyVariables());
    }

    public function getCount(array $whereConditions = []) : int
    {
        $data = $this->getRecords(
            $whereConditions,
            0
        );

        return $data->offsetGet(ERH::NODE_HITS_TOTAL);
    }

    protected function getUrl(This\Data\ElasticBase $elasticData, string $uri) : Http\Data\Uri
    {
        return $this->defaultUri->withPath(
            $this->createIndexName($elasticData->getIndexDateValue()) . 
            $uri
        );
    }

    protected function updateDataWithResult(This\Data\ElasticBase $elasticData, Psr\Http\Message\ResponseInterface $response) : This\Data\ElasticBase
    {
        /**
         * @var \Phalconeer\Http\Data\Response $response
         */
        $result = $response->bodyVariable(ERH::NODE_RESULT);

    // echo \Phalconeer\Helper\TVarDumper::dump($result);die();

        switch ($result) {
            case ERH::VALUE_CREATED:
                return $elasticData->setId($response->bodyVariable(ERH::NODE_ID))
                    ->setIndex($response->bodyVariable(ERH::NODE_INDEX));
            case ERH::VALUE_UPDATED:
                return $elasticData->setIndex($response->bodyVariable(ERH::NODE_INDEX));
        }

        return $elasticData;
    }

    public function save(
        Dto\ArrayObjectExporterInterface $data,
        $forceInsert = false,
        $insertMode = Dao\Helper\DaoHelper::INSERT_MODE_NORMAL,
        $blockOperationOnWrongSequence = false
    ) : ?Dto\ImmutableDto
    {
        if (!$data instanceof This\Data\ElasticBase) {
            throw new This\Exception\InvalidDataObjectException(
                'Expected ElasticBase received: ' . get_class($data),
                This\Helper\ExceptionHelper::ELASTIC_DAO_BASE__INVALID_DATA_OBJECT_TO_SAVE
            );
        }
        /**
         * @var \Phalconeer\ElasticAdapter\Data\ElasticBase $data
         */
        $newRecord = !$data->isStored() || $forceInsert;

        $primaryKeyValue = $data->getPrimaryKeyValue();
        [$method, $uri] = EQRH::generateIndexUri($newRecord, implode('-', $primaryKeyValue));

        $url = $this->getUrl($data, $uri);
        if ($blockOperationOnWrongSequence
            && !is_null($data->sequenceNumber())
            && !is_null($data->primaryTerm())) {
            $url = $url->withQueryVariable(EQRH::VAR_IF_SEQ_NO, $data->sequenceNumber())
                ->withQueryVariable(EQRH::VAR_IF_PRIMARY_TERM, $data->primaryTerm());
        }

        $request = Http\Data\Request::fromArray([
            'method'            => $method,
            'url'               => $url,
            'bodyVariables'     => $data->export()
        ]);

    // echo \Phalconeer\Dev\TVarDumper::dump($request);die();

        return $this->updateDataWithResult($data, $this->browser->call($request));
    }

    public function delete(
        array $whereConditions = [],
        int $primaryTerm = null,
        int $sequenceNumber = null
    ) : bool
    {
        $url = $this->defaultUri->withPath($this->indexName . '/' . EQRH::URI_DELETE_BY_QUERY);
        if (!is_null($primaryTerm)
            && !is_null($sequenceNumber)) {
            $url = $url->withQueryVariable(EQRH::VAR_IF_SEQ_NO, $sequenceNumber)
                ->withQueryVariable(EQRH::VAR_IF_PRIMARY_TERM, $primaryTerm);
        }


        $request = Http\Data\Request::fromArray([
            'method'            => Http\Helper\HttpHelper::HTTP_METHOD_POST,
            'url'               => $url,
            'bodyVariables'     => [
                EQBH::NODE_QUERY    => EQH::buildQuery($whereConditions)
            ]
        ]);

        $response = $this->browser->call($request);
        /**
         * @var \Phalconeer\Http\Data\Response $response
         */
        $total = $response->bodyVariable('total');
        $deleted = $response->bodyVariable('deleted');

        return $total === $deleted
                && $deleted > 0;
    }
}