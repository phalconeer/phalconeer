<?php
namespace Phalconeer\ElasticAdapter\Dao;

use ArrayObject;
use DateTime;
use Psr\Http\Message\ResponseInterface;
use Phalcon\Config;
use Phalconeer\Module\Dao\DaoReadAndWriteInterface;
use Phalconeer\Module\Dao\DaoReadInterface;
use Phalconeer\Module\Dao\DaoWriteInterface;
use Phalconeer\Module\Dao\Helper\DaoHelper;
use Phalconeer\Module\Dto\ImmutableObject;
use Phalconeer\Module\Browser\Bo\BrowserBo;
use Phalconeer\Module\ElasticAdapter\Exception\InvalidBrowserInstanceException;
use Phalconeer\Module\ElasticAdapter\Helper\ElasticQueryBodyHelper;
use Phalconeer\Module\ElasticAdapter\Helper\ElasticQueryHelper;
use Phalconeer\Module\ElasticAdapter\Helper\ElasticQueryRouteHelper;
use Phalconeer\Module\ElasticAdapter\Helper\ElasticResponseHelper;
use Phalconeer\Module\Http\Dto\Request;
use Phalconeer\Module\Http\Dto\Uri;
use Phalconeer\Module\Http\Helper\HttpHelper;
use Phalconeer\Module\ElasticAdapter\Dto\ElasticBase;

class ElasticDaoBase implements DaoReadInterface, DaoWriteInterface, DaoReadAndWriteInterface
{
    const MAX_DELETE_ONCE = 1000;

    /**
     * @var \Phalconeer\Module\Browser\Bo\BrowserBo
     */
    protected $browser;

    /**
     * @var \Phalcon\Config
     */
    protected $config;

    /**
     *@var Phalconeer\Module\Http\Dto\Uri
     */
    protected $defaultUri;

    /**
     * Index name. If index is date segmented, it serves as a prefix;
     * @var string
     */
    protected $indexName;

    /**
     * Format to use when generating the various date based index names.
     * Help on what format strings mean: http://php.net/manual/en/dateinterval.format.php
     * @var string
     */
    protected $indexSegmentFormat = 'Y.m';

    /**
     * The constructor.
     *
     * @param array $connections
     */
    public function __construct(
        Config $config
    )
    {
        if (!$config->browser instanceof BrowserBo) {
            throw new InvalidBrowserInstanceException(static::class);
        }
        $this->browser = $config->browser;
        $this->config = $config;
        $this->defaultUri = (new Uri)
            ->withScheme($this->config->protocol)
            ->withHost($this->config->host)
            ->withPort($this->config->port);
        if ($this->config->offsetExists('indexName')) {
            $this->indexName = $this->config->offsetGet('indexName');
        }
    }

    /**
     * Generates a dateSegmented index name.
     * @param \DateTime $date
     * @return string
     */
    public function createIndexName(DateTime $date = null)
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
    ) : ?ArrayObject
    {
        $result = $this->getRecords(
            $whereConditions,
            1,
            0,
            '',
            $getSequenceInformation
        );

        return new ArrayObject($result->offsetGet('hits')[0]);
    }

    public function getRecords(
        array $whereConditions = [],
        int $limit = 20,
        int $offset = 0,
        string $orderString = '',
        bool $getSequenceInformation = false
    ) : ?ArrayObject
    {
        $url = $this->defaultUri->withPath($this->indexName . '/_search')
                ->withQuery(ElasticQueryRouteHelper::VAR_IGNORE_UNAVAILBLE . '=true');

        $sort = ElasticQueryHelper::buildOrderClause($orderString);
        $query = ElasticQueryHelper::buildQuery($whereConditions);

        $bodyVariables = [
                ElasticQueryBodyHelper::NODE_FROM     => $offset,
                ElasticQueryBodyHelper::NODE_SIZE     => $limit,
            ];
        if (!empty($sort)) {
            $bodyVariables[ElasticQueryBodyHelper::NODE_SORT] = $sort;
        }
        if (!empty($query)) {
            $bodyVariables[ElasticQueryBodyHelper::NODE_QUERY] = $query;
        }
        if ($getSequenceInformation) {
            $bodyVariables[ElasticQueryBodyHelper::NODE_SEQ_NO_PRIMARY_TERM] = true;
        }

        $request = new Request([
            'method'            => HttpHelper::HTTP_METHOD_POST,
            'url'               => $url,
            'bodyVariables'     => $bodyVariables
        ]);

        $response = $this->browser->call($request);
// echo $limit . PHP_EOL;
// echo \Phalconeer\Helper\TVarDumper::dump($request);
// echo \Phalconeer\Helper\TVarDumper::dump($response);

        return new ArrayObject($response->bodyVariables());
    }

    public function getCount(array $whereConditions = []) : int
    {
        $data = $this->getRecords(
            $whereConditions,
            0
        );

        return $data->offsetGet(ElasticResponseHelper::NODE_HITS_TOTAL);
    }

    protected function getUrl(ElasticBase $elasticData, string $uri) : Uri
    {
        return $this->defaultUri->withPath(
            $this->createIndexName($elasticData->getIndexDateValue()) . 
            $uri
        );
    }

    protected function updateDataWithResult(ElasticBase $elasticData, ResponseInterface $response) : ElasticBase
    {
        $result = $response->bodyVariable(ElasticResponseHelper::NODE_RESULT);

    // echo \Phalconeer\Helper\TVarDumper::dump($result);die();

        switch ($result) {
            case ElasticResponseHelper::VALUE_CREATED:
                return $elasticData->setId($response->bodyVariable(ElasticResponseHelper::NODE_ID))
                    ->setIndex($response->bodyVariable(ElasticResponseHelper::NODE_INDEX));
            case ElasticResponseHelper::VALUE_UPDATED:
                return $elasticData->setIndex($response->bodyVariable(ElasticResponseHelper::NODE_INDEX));
        }

        return $elasticData;
    }

    public function save(
        ImmutableObject $data,
        $forceInsert = false,
        $insertMode = DaoHelper::INSERT_MODE_NORMAL,
        $blockOperationOnWrongSequence = false
    ) : ?ImmutableObject
    {
        $newRecord = !$data->isStored() || $forceInsert;

        $primaryKeyValue = $data->getPrimaryKeyValue();
        [$method, $uri] = ElasticQueryRouteHelper::generateIndexUri($newRecord, implode('-', $primaryKeyValue));

        $url = $this->getUrl($data, $uri);
        if ($blockOperationOnWrongSequence
            && !is_null($data->sequenceNumber())
            && !is_null($data->primaryTerm())) {
            $url = $url->withQueryVariable(ElasticQueryRouteHelper::VAR_IF_SEQ_NO, $data->sequenceNumber())
                ->withQueryVariable(ElasticQueryRouteHelper::VAR_IF_PRIMARY_TERM, $data->primaryTerm());
        }

        $masked = clone($data);
        $masked->setExlcudeMask(['id', 'index', 'sequenceNumber', 'primaryTerm']);
        $request = new Request([
            'method'            => $method,
            'url'               => $url,
            'bodyVariables'     => $masked->toArrayCopy(true, false)
        ]);

    // echo \Phalconeer\Helper\TVarDumper::dump($request);die();

        return $this->updateDataWithResult($data, $this->browser->call($request));
    }

    public function delete(
        array $whereConditions = [],
        int $primaryTerm = null,
        int $sequenceNumber = null
    ) : bool
    {
        $url = $this->defaultUri->withPath($this->indexName . '/' . ElasticQueryRouteHelper::URI_DELETE_BY_QUERY);
        if (!is_null($primaryTerm)
            && !is_null($sequenceNumber)) {
            $url = $url->withQueryVariable(ElasticQueryRouteHelper::VAR_IF_SEQ_NO, $sequenceNumber)
                ->withQueryVariable(ElasticQueryRouteHelper::VAR_IF_PRIMARY_TERM, $primaryTerm);
        }


        $request = new Request([
            'method'            => HttpHelper::HTTP_METHOD_POST,
            'url'               => $url,
            'bodyVariables'     => [
                ElasticQueryBodyHelper::NODE_QUERY    => ElasticQueryHelper::buildQuery($whereConditions)
            ]
        ]);

        $response = $this->browser->call($request);

        $total = $response->bodyVariable('total');
        $deleted = $response->bodyVariable('deleted');

        return $total === $deleted
                && $deleted > 0;
    }
}