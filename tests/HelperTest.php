<?php

namespace Tests;

use App\ExposeApi\Recipe\Exception\ExposeApiInvalidArgument;
use PHPUnit\Framework\TestCase;

class HelperTest extends TestCase
{

    /**
     * @throws \TypeError
     */
    public function test_plug_function_inputs_need_closure_as_second_argument()
    {
        $this->expectException(\TypeError::class);

        plug('', null);
    }

    /**
     * @throws \TypeError
     */
    public function test_plug_function_should_return_input_as_function_return()
    {
        $fake = "this is for test";

        $this->assertEquals(
            $fake, plug(
                $fake, function () {
                }
            )
        );
    }

    public function test_getAuth_function_return_AccessKey()
    {
        $iterator = new \ArrayIterator(
            [
            "Authorization" => "AccessKey this_is_accesss_key"
            ]
        );

        $this->assertEquals("this_is_accesss_key", getAuth($iterator));
    }


    public function test_getAuth_function_headers_should_contains_AccessKey_keyword()
    {
        $iterator = new \ArrayIterator(
            [
            "Authorization" => "this_is_just_string",
            "Content-Type" => "application/json"
            ]
        );

        $this->expectException(ExposeApiInvalidArgument::class);
        getAuth($iterator);


    }




}