<?php

namespace App\ExposeApi\Core;

use Exception;
use Ramsey\Uuid\{
    Uuid, UuidInterface
};

class AccumulateId implements AccumulateIdInterface
{
    /**
     * @var string
     */
    protected $id;

    /**
     * UserId constructor.
     *
     * @param UuidInterface $id
     */
    public function __construct(UuidInterface $id)
    {
        $this->id = $id;
    }

    /**
     * @param  string $string
     * @return AccumulateIdInterface
     */
    public static function rebuild($string)
    {
        return new static(Uuid::fromString($string));
    }

    /**
     * generate proper uuid4 value
     *
     * @return UuidInterface
     * @throws Exception
     */
    public static function generate()
    {
        return Uuid::uuid4();
    }


    public function __toString()
    {
        return $this->id;
    }

}