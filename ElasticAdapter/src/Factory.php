<?php
namespace Phalconeer\ElasticAdapter;

use Phalconeer\Bootstrap;
use Phalconeer\Browser;
use Phalconeer\Config;
use Phalconeer\CurlClient;
use Phalconeer\ElasticAdapter as This;
use Phalconeer\Http;

class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'elasticAdapter';

    protected static array $requiredModules = [
        Config\Factory::MODULE_NAME,
        Browser\Factory::MODULE_NAME,
        Http\Factory::MODULE_NAME,
        CurlClient\Factory::MODULE_NAME,
    ];

    protected static array $configFiles = [];

    protected function getDefaultRequestMiddlewares() : array
    {
        return [
            new This\Bo\ElasticRequestTransformer(),
        ];
    }

    protected function getDefaultResponseMiddlewares() : array
    {
        return [
            new This\Bo\ElasticResponseTransformer(),
            new This\Bo\ElasticResponseErrorTransformer(),
            new This\Bo\ElasticResponseCreatedTransformer(),
            new This\Bo\ElasticResponseUpdatedTransformer(),
            new This\Bo\ElasticResponseAggregateTransformer(),
            new This\Bo\ElasticResponseSimpleResultTransformer()
        ];
    }

    protected function configure() {
        $connectionParameters = $this->di->get(Config\Factory::MODULE_NAME)
            ->get('elasticsearch', Config\Helper\ConfigHelper::$dummyConfig);
        $di = $this->di;
        $defaultRequestMiddlewares = $this->getDefaultRequestMiddlewares();
        $defaultResponseMiddlewares = $this->getDefaultResponseMiddlewares();

        return function (
            $connectionType = This\HElper\ElasticAdapterHelper::DEFAULT_CONFIG,
            array $requestMiddlewares = [],
            array $responseMiddlewares = []
        ) use ($connectionParameters, $di, $defaultRequestMiddlewares, $defaultResponseMiddlewares) {
            if (is_null($connectionParameters)
                || !$connectionParameters->offsetExists($connectionType)) {
                throw new This\Exception\ElasticConfigurationNotFoundException($connectionType);
            }
            foreach ($defaultRequestMiddlewares as $middleware) {
                $requestMiddlewares[] = $middleware;
            }
            foreach ($defaultResponseMiddlewares as $middleware) {
                $responseMiddlewares[] = $middleware;
            }
            $config = clone($connectionParameters->get($connectionType, Config\Helper\ConfigHelper::$dummyConfig));

            $curlOptions = new CurlClient\Data\CurlOptions();

            if ($config->has('xpack')
                && $config->xpack->has('username')) {
                $curlOptions = $curlOptions->setHttpAuth(CURLAUTH_BASIC)
                                    ->setUserPwd($config->xpack->username . ':' . $config->xpack->password);
            }

            return new This\Data\ElasticDaoConfiguration(new \ArrayObject([
                'browser'       => $di->get(
                    Browser\Factory::MODULE_NAME,
                    [
                        $di->get(CurlClient\Factory::MODULE_NAME, [$curlOptions]),
                        $requestMiddlewares,
                        $responseMiddlewares
                    ]
                ),
                'config'        => $config,
                'defaultUri'    => (new Http\Data\Uri())->withScheme($config->get('protocol', 'http'))
                    ->withHost($config->get('host', 'localhost'))
                    ->withPort($config->get('port', 9200))
            ]));
        };
    }
}