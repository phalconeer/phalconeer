<?php
namespace Phalconeer\RestResponse;

use Phalconeer\Data;

/**
 * Interface for the entities used by our REST API system.
 */
interface ResourceInterface extends Data\CommonInterface
{

    /**
     * Returns the type of a resource.
     */
    public function getResourceType() : string;

    /**
     * Converts the entity into a specific format.
     */
    public function convertTo(string $format) : string;

    public function addMeta($key, $value) : self;

    public function addLink($key, $value) : self;
}
