<?php

namespace App\ExposeApi\Core;


interface EventInterface
{
    /**
     * event type
     *
     * @return string
     */
    public static function getType() : string ;

    /**
     * get full qualified namespace prefix
     *
     * @return string
     */
    public static function getContext(): string;

    /**
     * get the full qualified namespace based on input
     *
     * @param  string $event
     * @return string
     */
    public static function getContextFromType(string $event): string;


    /**
     * event handler
     *
     * @return string
     */
    public function handle();

}