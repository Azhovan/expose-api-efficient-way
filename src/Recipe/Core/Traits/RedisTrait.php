<?php

namespace App\ExposeApi\Recipe\Core\Traits;

use App\ExposeApi\Core\RedisPersistence;
use App\ExposeApi\Recipe\Exception\ExposeApiInvalidArgument;

trait RedisTrait
{
    /**
     * get Driver for this implementation.
     *
     * @return \Predis\Client
     */
    public static function getPersistentDriver()
    {
        return new \Predis\Client([
            'host'   => 'redis',
            'port'   => 6379,
        ]);
    }

    /**
     * return value(s) of key(s) if exists, nor return exception.
     *
     * @param  $key
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function getOrFail($key)
    {
        // return one result
        if ($this->has($key)) {
            return $this->persistenceDriver->get($key);
        }

        throw new ExposeApiInvalidArgument('key not found', 404);
    }

    /**
     * delete item, update the disk Asynchronously.
     *
     * @param  $key
     *
     * @throws \Exception
     *
     * @return int
     */
    public function deleteOrFail($key): int
    {
        if (!$this->has($key)) {
            throw new ExposeApiInvalidArgument('key does not exists', 404);
        }

        $result = $this->persistenceDriver->del($key);
        $this->saveAsync();

        return $result;
    }

    /**
     *  Asynchronously save the dataset to disk (in background).
     *
     * @return mixed
     */
    public function saveAsync()
    {
        return dispatch(RedisPersistence::getContextFromType('RedisPersistence'), $this->persistenceDriver);
    }

    /**
     * Append a value to stored key.
     *
     * @param  $key
     * @param  $item
     * @param  $postFix
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function append($key, $item, $postFix): string
    {
        // if key not exist, throw error
        $this->getOrFail($key);

        // create new key to hold all rates (relation 1:many)
        // new key
        $key = $key.$postFix;
        $values = $this->getOrCreate($key);

        $valueArray = json_decode($values, true);
        array_push($valueArray, $item);

        $this->save($key, json_encode($valueArray));

        return json_encode(
            [
                'rates' => $valueArray,
            ]
        );
    }

    /**
     * get a key's value if exist, nether create an empty one.
     *
     * @param  $key
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function getOrCreate($key)
    {
        if (!$this->has($key)) {
            $this->save($key, '{}');
        }

        return $this->get($key);
    }

    /**
     * save and persist data on disk Asynchronously.
     *
     * @param $key
     * @param $value
     */
    public function save($key, $value): void
    {
        $this->persistenceDriver->set($key, $value);

        $this->saveAsync();
    }

    /**
     * return value(s) of key(s).
     *
     * @param  $key
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function get($key)
    {
        if ($this->has($key)) {
            return $this->persistenceDriver->get($key);
        } elseif ($key == '*') {
            $keys = $this->persistenceDriver->keys('*');

            return $this->persistenceDriver->mGet($keys);
        }

        throw new ExposeApiInvalidArgument('key not found', 404);
    }

    /**
     * check whether key exist or not.
     *
     * @param  $key
     *
     * @return int
     */
    public function has($key)
    {
        return $this->persistenceDriver->exists($key);
    }

    /**
     * Generator to loop through all keys efficiently.
     *
     * @param  $keys
     *
     * @return \Generator
     */
    private function nextCursor($keys)
    {
        foreach ($keys as $value) {
            yield $value;
        }
    }

    /**
     * Search any combination of search.
     *
     * @param  $needle
     *
     * @throws \Exception
     *
     * @return string
     */
    public function search(string $needle)
    {
        if (empty($needle)) {
            throw new ExposeApiInvalidArgument('search elements are empty');
        }

        $found = [];
        $pattern = str_replace(['{', '}'], '', $needle);

        foreach ($this->nextCursor($this->get('*')) as $value) {
            if (strstr($value, $pattern)) {
                $found[] = ($value);
            }
        }

        if (count($found) > 0) {
            return json_encode($found);
        }

        throw new \Exception('Recipe does not found with details given.');
    }

    /**
     * delete all keys in redis.
     *
     * @return void
     */
    public function clean()
    {
        $this->persistenceDriver->flushAll();
    }
}
