<?php

namespace Cyclonecode\Plugin\Cache;

use Cyclonecode\Plugin\Singleton;

class Transient extends Singleton implements CacheInterface
{
    public function get($key)
    {
        return get_transient($key);
    }

    public function set($key, $value, $ttl = 0)
    {
        return set_transient($key, $value, $ttl);
    }

    public function delete($key)
    {
        return delete_transient($key);
    }

    public function exists($key)
    {
        $foo = !!$this->get($key);
        return $foo;
    }
}
