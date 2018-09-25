<?php

namespace App\ExposeApi\Core;

class RedisPersistence implements EventInterface
{

    /**
     * @var int retry after seconds 
     */
    private const DELAY_TO_PERSIST = 2;

    /**
     * @var mixed redis driver 
     */
    private $driver;

    /**
     * @var int count of retries 
     */
    private $retry = 1;

    /**
     * RedisPersistence constructor.
     *
     * @param $driver
     */
    public function __construct(&$driver)
    {
        $this->driver = $driver;
    }

    /**
     * @inheritdoc
     *
     * @return string
     */
    public static function getType(): string
    {
        return "Redis";
    }

    /**
     * @inheritdoc
     *
     * @return string
     */
    public static function getContext(): string
    {
        return "\\App\\ExposeApi\\Core\\";
    }

    /**
     * @inheritdoc
     *
     * @param  $event
     * @return string
     */
    public static function getContextFromType(string $event): string
    {
        return "\\App\\ExposeApi\\Core\\{$event}";
    }

    /**
     * Persist data on disk
     * exception will happen if the prevoius job is still in progress
     * we delayed the tasks, and retry it after few seconds
     *
     * @inheritdoc
     *
     * @return string
     */
    public function handle()
    {
        try {

            $this->driver->bgSave();

        } catch (\Exception $exception) {
            $this->retry();
        }
    }

    /**
     * retry policy
     */
    private function retry(): void
    {
        if ($this->retry <= 4) {

            sleep($this->retry * self::DELAY_TO_PERSIST);
            $this->retry++;

            $this->handle();
        }

    }
}