<?php
namespace Phalconeer\Memcache\Bo;

use Phalcon\Cache\Adapter;
use Phalcon\Storage;
use Phalconeer\Cache;

class MemcacheAdapter extends Adapter\Libmemcached implements Cache\CacheAdapterInterfaceWithIgnore
{
    protected Cache\Data\CacheSettings $cacheSettings;

    public function __construct(
        Storage\SerializerFactory $factory,
        array $options = []
    )
    {
        parent::__construct($factory, $options);

        $this->cacheSettings = $options['cacheSettings'];
    }

    public function ignoreCache()
    {
        $this->cacheSettings = $this->cacheSettings->setIgnoreCache(true);
    }

    public function resetIgnoreCache()
    {
        if ($this->cacheSettings->ignoreCache()) {
            $this->cacheSettings = $this->cacheSettings->setIgnoreCache(false);
        }
    }

//     /**
//      * Checks the serializer. If it is a supported one it is set, otherwise
//      * the custom one is set.
//      *
//      * @param \Memcached $connection
//      */
//     private function setSerializer(\Memcached $connection)
//     {
//         $map = [
//             "php"      => \Memcached::SERIALIZER_PHP,
//             "json"     => \Memcached::SERIALIZER_JSON,
//             "igbinary" => \Memcached::SERIALIZER_IGBINARY
//         ];

//         $serializer = strtolower($this->defaultSerializer);

//         if (isset($map[$serializer])) {
//             $this->defaultSerializer = "";
//             $connection->setOption(\Memcached::OPT_SERIALIZER, $map[$serializer]);
//         } else {
//             $this->initSerializer();
//         }
//     }

//     public function getAdapter()
//     {
//         if (null === $this->adapter) {
//             $options      = $this->options;
//             $persistentId = $options["persistentId"] ??  "ph-mcid-";

//         // echo $persistentId;
//             $sasl         = $options["saslAuthData"] ??  [];
//             $connection   = new \Memcached($persistentId);
//             $serverList   = $connection->getServerList();

//             $connection->setOption(\Memcached::OPT_PREFIX_KEY, $this->prefix);

//             if (count($serverList) < 1) {
//                 $servers  = $options["servers"] ??  [];
//                 $client   = $options["client"] ?? [];
//                 $saslUser = $sasl["user"] ??  "";
//                 $saslPass = $sasl["pass"] ?? "";
//                     $failover = [
//                         \Memcached::OPT_CONNECT_TIMEOUT       => 10,
//                         \Memcached::OPT_DISTRIBUTION          => \Memcached::DISTRIBUTION_CONSISTENT,
//                         \Memcached::OPT_SERVER_FAILURE_LIMIT  => 2,
//                         \Memcached::OPT_REMOVE_FAILED_SERVERS => true,
//                         \Memcached::OPT_RETRY_TIMEOUT         => 1
//                     ];
//                     $client   = array_merge($failover, $client);

//                 if (!$connection->setOptions($client)) {
//                     throw new \Exception("Cannot set Memcached client options");
//                 }

//                 if (!$connection->addServers($servers)) {
//                     throw new \Exception("Cannot connect to the Memcached server(s)");
//                 }

//                 if (!empty($saslUser)) {
//                     $connection->setSaslAuthData($saslUser, $saslPass);
//                 }
//             }

//             $this->setSerializer($connection);

//             $this->adapter = $connection;
//         }
// echo \Phalconeer\Helper\TVarDumper::dump($this->adapter);
//         return $this->adapter;
//     }

    public function get(string $key, $defaultValue = null)
    {
        if (!$this->cacheSettings->readCache()) {
            $this->resetIgnoreCache();
            return null;
        }
        $value = parent::get($key, $defaultValue);

        switch (true) {
            case $value instanceof \stdClass:
                return new \ArrayObject(get_object_vars($value));
            case is_array($value):
                return new \ArrayObject($value);
            default:
                return $value;
        }
    }

    public function set(string $key, $value, $ttl = null) : bool
    {
        if (is_null($value)) {
            return true;
        }

        if (!$this->cacheSettings->writeCache()) {
            $this->resetIgnoreCache();
            return true;
        }

        return parent::set(
            $key,
            $value,
            $ttl
        );
    }

    public function has(string $key) : bool
    {
        if (!$this->cacheSettings->readCache()) {
            $this->resetIgnoreCache();
            return false;
        }
        return parent::has($key);
    }

    /**
     * TODO: THIS FUNCTION IS FOR DEBUGGING ONLY
     */
    public function getResultCode()
    {
        return $this->getAdapter()->getResultCode();
    }

}