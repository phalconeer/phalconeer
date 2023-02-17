<?php
namespace Phalconeer\Auth\Bo;

use Phalconeer\Dao;
use Phalconeer\Application;
use Phalconeer\Auth as This;

class ScopeRepository implements This\ScopeRepositoryInterface
{
    public function __construct(
        protected Application\ApplicationInterface $application,
        protected This\PrivilegesDaoInterface $privilegesDao
    )
    {
        $this->application = $application;
        $this->privilegesDao = $privilegesDao;
    }

    public function getScopeNames(array $privileges, string $privilegeScheme = null) : \ArrayObject
    {
        if (count($privileges) === 0) {
            return new \ArrayObject();
        }
        if (is_null($privilegeScheme)) {
            $privilegeScheme = $this->application->getPrivilegeScheme();
        }

        $privilegeData = $this->privilegesDao->getPrivilegeFullNames([
            'p.id' => $privileges
        ]);

        $iterator = $privilegeData->getIterator();
        $privilegeFullNames = new \ArrayObject();

        while ($iterator->valid()) {
            $privilegeFullNames->offsetSet(
                $iterator->current()->offsetGet('id'),
                implode(
                    '.',
                    array_filter(
                        [
                            $privilegeScheme,
                            $iterator->current()->offsetGet('resource'),
                            $iterator->current()->offsetGet('privilege')
                        ]
                    )
                )
            );
            $iterator->next();
        }

        return $privilegeFullNames;
    }
}