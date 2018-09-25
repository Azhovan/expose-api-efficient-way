<?php

namespace App\ExposeApi\Recipe\Core\Event;

use App\ExposeApi\Core\EventInterface;
use IteratorAggregate;

abstract class AbstractRecipeEvent implements EventInterface
{

    /**
     * @var IteratorAggregate
     */
    protected $data;

    protected $persistenceDriver;


    /**
     * RecipeCreated constructor.
     *
     * @param IteratorAggregate $data
     */
    public function __construct(IteratorAggregate $data)
    {
        $this->data = $data;
        $this->persistenceDriver = static::getPersistentDriver();
    }

    /**
     * @inheritdoc
     *
     * @return string
     */
    public static function getType(): string
    {
        return "Recipe";
    }

    /**
     * @inheritdoc
     *
     * @return string
     */
    public static function getContext(): string
    {
        return "\\App\\ExposeApi\\Recipe\\Core\\Event\\";
    }

    /**
     * @inheritdoc
     *
     * @param  $event
     * @return string
     */
    public static function getContextFromType(string $event): string
    {
        return "\\App\\ExposeApi\\Recipe\\Core\\Event\\{$event}";
    }

    /**
     * @inheritdoc
     *
     * @return string
     */
    public abstract function handle();


}