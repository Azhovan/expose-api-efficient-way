<?php


if (!function_exists('plug')) {

    /**
     *  Call the given Closure with the given value then return the value.
     *
     * @param $value
     * @param $callback
     *
     * @throws TypeError
     *
     * @return mixed
     */
    function plug($value, callable $callback)
    {
        if (!is_callable($callback)) {
            throw new \TypeError('callback is not callable');
        }

        $callback($value);

        return $value;
    }
}

if (!function_exists('dispacth')) {

    /**
     * autoload event handler, return result.
     *
     * @param  $context
     * @param  $data
     *
     * @return mixed
     */
    function dispatch($context, $data)
    {
        $event = new $context($data);

        return $event->handle();
    }
}

if (!function_exists('getAuth')) {

    /**
     * Get AccessKey id needed.
     *
     * @param  $headers
     *
     * @return mixed
     */
    function getAuth(Traversable $headers)
    {
        foreach ($headers as $key => $value) {
            if ('Authorization' == $key && strstr($value, 'AccessKey')) {
                $accessKey = trim(str_replace('AccessKey', '', $value));

                if (!empty($accessKey)) {
                    return $accessKey;
                }
            }
        }

        throw new \App\ExposeApi\Recipe\Exception\ExposeApiInvalidArgument('AccessKey is required', 401);
    }
}
