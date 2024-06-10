<?php
namespace Phalconeer\Router\Helper;

class RouterHelper
{
    const ACTION = 'action';

    const BEFORE_MATCH = 'beforeMatch';

    const CONTROLLER = 'controller';

    const METHODS = 'methods';

    const MODULE = 'module';

    const NAMESPACE = 'namespace';

    const PARAMETERS = 'parameters';

    const PREFIX = 'prefix';

    const ROUTE = 'route';

    const ROUTES = 'routes';

    /**
     * Helper method which provides a unique routing table identifier basd on the path
     * It is mostly just redundant work to make a unique identifier,
     * but in some cases the routing table needs to be replaces by athe child modules
     */
    public static function getUniqueNamespace(string $path) : string
    {
        $uniqueNamesapce = '';
        $pathPieces = explode('/', $path);
        foreach ($pathPieces as $folder) {
            if (!empty($folder)
                && strtolower($folder[0]) !== $folder[0]) {
                $uniqueNamesapce .= $folder;
            }
        }

        return $uniqueNamesapce;
    }
}