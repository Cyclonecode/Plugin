<?php

namespace Cyclonecode\Plugin\Cache;

interface CacheInterface
{
    /**
     * @param string $key
     * @return mixed
     */
    public function get($key);

    /**
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public function set($key, $value, $ttl = 0);

    /**
     * @param string $key
     * @return mixed
     */
    public function delete($key);

    /**
     * @param string $key
     * @return mixed
     */
    public function exists($key);
}
