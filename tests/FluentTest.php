<?php

namespace Tests;

use App\ExposeApi\Core\Fluent;
use PHPUnit\Framework\TestCase;

class FluentTest extends TestCase
{

    public function test_fluent_class_should_implements_JsonSerializable_Arrayable_Countable()
    {
        $fluentObject = new Fluent([]);

        $this->assertTrue($fluentObject instanceof \App\ExposeApi\Core\Contracts\Arrayable);
        $this->assertTrue($fluentObject instanceof \Countable);
        $this->assertTrue($fluentObject instanceof \JsonSerializable);
    }


    public function test_constructor_inputs_is_array()
    {
        $this->expectException(\TypeError::class);
        new Fluent('');
    }

    public function test_toJson_function_return_string()
    {
        $testArray = ["name" => "testname", "id" => "4df604aa-0693-4d8e-ad26-6f3eca4d81b6"];
        $fluentObject = new Fluent($testArray);

        $this->assertSame(json_encode($testArray), $fluentObject->toJson());
    }

    public function test_toArray_function_return_string()
    {
        $testArray = ["name" => "testname", "id" => "4df604aa-0693-4d8e-ad26-6f3eca4d81b6"];
        $fluentObject = new Fluent($testArray);

        $this->assertSame($testArray, $fluentObject->jsonSerialize());
        $this->assertSame($testArray, $fluentObject->toArray());
    }

    public function test_append_function_will_add_elements_to_attributes()
    {
        $testArray = ["name" => "testname", "id" => "4df604aa-0693-4d8e-ad26-6f3eca4d81b6"];

        $fluentObject = new Fluent($testArray);
        $fluentObject->append("newKey", "newValue");

        $this->assertSame("newValue", $fluentObject->toArray()["newKey"]);
    }

    public function test_key_function_return_specific_value_of_a_key()
    {
        $testArray = ["name" => "testname", "id" => "4df604aa-0693-4d8e-ad26-6f3eca4d81b6"];
        $fluentObject = new Fluent($testArray);

        $this->assertSame("testname", $fluentObject->key("name"));
    }

    public function test_key_function_doesnot_return_value_of_invalid_key()
    {
        $testArray = ["name" => "testname", "id" => "4df604aa-0693-4d8e-ad26-6f3eca4d81b6"];
        $fluentObject = new Fluent($testArray);

        $this->assertNull($fluentObject->key("non-existense-key"));
    }

    public function test_count_of_elements_function()
    {
        $testArray = [
            "name" => "testname",
            "id" => "4df604aa-0693-4d8e-ad26-6f3eca4d81b6",
            "prepTime" => "21 min",
            "vegetarian" => true
        ];
        $fluentObject = new Fluent($testArray);

        $this->assertEquals(4, $fluentObject->count());
    }


}