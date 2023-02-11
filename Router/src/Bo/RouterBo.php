<?php
namespace Phalconeer\Router\Bo;

use Phalcon\Mvc;
use Phalcon\Mvc\Router;
use Phalcon\Config;
use Phalconeer\Router as This;

class RouterBo
{

    public function __construct(
        protected Mvc\Router $router,
        protected Config\Config $routerConfiguration,
        protected Config\Config $applicationConfiguration
    )
    {
        $this->baseSetup();
        $this->loadRoutes();
    }

    /**
     * Creates the router object and sets the default values for it.
     */
    protected function baseSetup()
    {
        // $this->router->setUriSource(Mvc\Router::URI_SOURCE_SERVER_REQUEST_URI);
        $this->router->notFound([
            'controller' => $this->applicationConfiguration->get('errorController', 'index'),
            'action'     => $this->applicationConfiguration->get('errorNotFoundAction', 'error404'),
        ]);
        $this->router->removeExtraSlashes(true);
    }

    protected function addRouteGroup(string $routeName, array $routeData)
    {
        $groupParameters = array_filter(
            $routeData,
            function ($key) {
                return in_array(
                    $key,
                    [
                        This\Helper\RouterHelper::CONTROLLER,
                        This\Helper\RouterHelper::MODULE,
                        This\Helper\RouterHelper::NAMESPACE,
                    ]
                );
            },
            ARRAY_FILTER_USE_KEY
        );

        $group = new Router\Group($groupParameters);
        if (array_key_exists(This\Helper\RouterHelper::PREFIX, $routeData)) {
            $group->setPrefix($routeData[This\Helper\RouterHelper::PREFIX]);
        }
        if (array_key_exists(This\Helper\RouterHelper::BEFORE_MATCH, $routeData)) {
            $group->beforeMatch(This\Helper\RouterHelper::BEFORE_MATCH);
        }

        foreach ($routeData[This\Helper\RouterHelper::ROUTES] as $subRouteName => $subRouteData) {
            $this->addRoute(
                implode('.', [
                    $routeName,
                    $subRouteName
                ]),
                array_merge($groupParameters, $subRouteData),
                $group
            );
        }

        $this->router->mount($group);
    }

    /**
     * Adds a route to the router, parameters are coming from routing_table.
     */
    protected function addRoute(
        string $routeName,
        array $routeData,
        $target = null
    )
    {
        if (is_null($target)) {
            $target = $this->router;
        }
        $route = $target->add(
                $routeData[This\Helper\RouterHelper::ROUTE],
                $routeData[This\Helper\RouterHelper::PARAMETERS]
            )->setName($routeName);
        /**
         * @var \Phalcon\Mvc\Route $route
         */

        if (array_key_exists(This\Helper\RouterHelper::METHODS, $routeData)) {
            $route->via($routeData[This\Helper\RouterHelper::METHODS]);
        }
        if (array_key_exists(This\Helper\RouterHelper::BEFORE_MATCH, $routeData)) {
            $route->beforeMatch($routeData['beforeMatch']);
        }
    }

    /**
     * Reads the routes for the route tables set in application config.
     */
    protected function includeRoutingTables() : array
    {
        if (!($this->routerConfiguration->get('routingTables') instanceof Config\Config)) {
            throw new This\Exception\NoRoutingTablesDefinedException(
                'No routing tables are found in the configuration',
                This\Helper\ExceptionHelper::ROUTER__NO_ROUTING_TABLES
            );
        }

        return array_reduce(
            $this->routerConfiguration->get('routingTables', new Config\Config())->toArray(),
            function (array $aggregate, string $fileName) {
                if (file_exists($fileName)) {
                    return array_merge_recursive($aggregate, include $fileName);
                }
                else {
                    throw new This\Exception\RoutingTableNotFoundException(
                        'Routing table not found:' . $fileName,
                        This\Helper\ExceptionHelper::ROUTER__ROUTING_TABLE_NOT_FOUND
                    );
                }
        }, []);
    }

    /**
     * Reads the routes when there are no language defined.
     */
    protected function loadRoutes()
    {
        $routes = $this->includeRoutingTables();
        foreach ($routes as $routeName => $routeData) {
            if (!array_key_exists(This\Helper\RouterHelper::ROUTES, $routeData)
                && !array_key_exists(This\Helper\RouterHelper::ROUTE, $routeData)) {
                throw new This\Exception\InvalidRouteDefinitionException(
                    $routeName,
                    This\Helper\ExceptionHelper::ROUTER__INVALID_ROUTE_DEFINITION
                );
                
            }
            if (array_key_exists(This\Helper\RouterHelper::ROUTES, $routeData)) {
                if (is_array($routeData[This\Helper\RouterHelper::CONTROLLER])) {
                    throw new This\Exception\MultipleDefinitionsForRouteException(
                        $routeName,
                        This\Helper\ExceptionHelper::ROUTER__MULTIPLE_GROUP_DEFINITIONS
                    );
                }
                $this->addRouteGroup($routeName, $routeData);
            } else {
                if (is_array($routeData[This\Helper\RouterHelper::ROUTE])) {
                    throw new This\Exception\MultipleDefinitionsForRouteException(
                        $routeName,
                        This\Helper\ExceptionHelper::ROUTER__MULTIPLE_DEFINITIONS
                    );
                }
                $this->addRoute($routeName, $routeData);
            }
        }
    // echo \Phalconeer\Dev\TVarDumper::dump($this->router->getRoutes());die();
    }

    public function getRouter() : Mvc\Router
    {
        return $this->router;
    }
}
