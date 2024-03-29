<?php

namespace App\Cache;

use Illuminate\Support\Facades\Cache;

abstract class AbstractCacheClass
{
    public $cahceObj;
    public function __construct(string $driver = null)
    {
        $this->cahceObj = app("cache")->driver($driver ?? config("cache.default"));
    }
    abstract public function getKey(string $key);

    public function getData(string $key): mixed
    {
        dump($this->getKey($key));
        return $this->cahceObj->get($this->getKey($key));
    }

    public function setData(string $key, mixed $data, int $seconds = null): mixed
    {
        dump($this->getKey($key));
        return $this->cahceObj->put($this->getKey($key), $data, $seconds);
    }

    public function forgetData(string $key): mixed
    {
        return $this->cahceObj->forget($this->getKey($key));
    }

    public function flush(): mixed
    {
        return $this->cahceObj->flush();
    }

    public function setDataForever(string $key, mixed $data): mixed
    {
        return $this->cahceObj->forever($this->getKey($key), $data);
    }


}
