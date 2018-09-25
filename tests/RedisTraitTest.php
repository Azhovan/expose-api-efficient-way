<?php

namespace Tests;

use App\ExposeApi\Recipe\Exception\ExposeApiInvalidArgument;
use App\ExposeApi\Recipe\Core\Traits\RedisTrait;
use PHPUnit\Framework\TestCase;
use Predis\Client as RedisClient;

class RedisTraitTest extends TestCase
{
    use RedisTrait;

    private const KEY_FOUND = 1;
    private const KEY_NOT_FOUND = 0;
    private const KEY_DELETED = 1;

    private $persistenceDriver;

    public function setUp()
    {
        parent::setUp();

        $this->persistenceDriver = RedisTrait::getPersistentDriver();
    }

    public function test_getPersistentDriver_should_return_redis_client()
    {
        $this->assertTrue($this->persistenceDriver instanceof RedisClient);
    }

    public function test_getOrCreate_function_create_empty_set_if_key_does_not_exists()
    {
        $this->getOrCreate('invalid_key');
        $this->assertSame("{}", $this->get('invalid_key'));
    }

    public function test_getOrCreate_function_return_set_if_key_does_exists()
    {
        $this->save('key', 'value');
        $this->assertSame("value", $this->getOrCreate('key'));
    }

    public function test_save_function_will_set_value_for_custom_key()
    {
        $this->save('key', 'value');

        $this->assertEquals('value', $this->get('key'));
    }

    public function test_function_get_return_all_sets_if_argument_is_wildcard()
    {
        $this->clean();;

        $this->save('key1', 'value1');
        $this->save('key2', 'value2');

        $expected = [
            "value1",
            "value2"
        ];

        $actual = $this->get("*");
        sort($actual);

        $this->assertEquals($expected, $actual);
    }

    public function test_get_function_throw_exception_if_key_not_found()
    {
        $this->expectException(ExposeApiInvalidArgument::class);
        $this->expectExceptionCode(404);

        $this->clean();
        $this->get("not-existed-key");
    }

    public function test_has_function_return_true_if_key_exists()
    {
        $this->clean();
        $this->save('key1', 'value1');
        $this->assertSame(self::KEY_FOUND, $this->has('key1'));
    }

    public function test_search_function_return_value_for_custom_key()
    {
        $this->clean();
        $expected = [
            "name" => "1b",
            "prepTime" => "2b",
            "vegetarian" => false,
            "difficulty" => "3b",
            "id" => "4df604aa-0693-4d8e-ad26-6f3eca4d81b6"
        ];

        $this->save("4df604aa-0693-4d8e-ad26-6f3eca4d81b6", json_encode($expected));

        $actual = json_decode($this->search("4df604aa-0693-4d8e-ad26-6f3eca4d81b6"), true);

        $this->assertSame(json_encode($expected), $actual[0]);
    }

    public function test_search_function_will_return_exception_if_key_not_found()
    {
        $this->expectException(\Exception::class);
        $this->clean();

        $needle = [
            "name" => "name",
            "prepTime" => "21 min",
            "difficulty" => "hard",
        ];
        $this->search(json_encode($needle));
    }

    public function test_invalid_needle_as_search_argument()
    {
        $this->expectException(ExposeApiInvalidArgument::class);
        $this->search('');
    }

    public function test_function_deleteOrFail_delete_a_key()
    {
        $this->clean();
        $this->save('key1', 'value1');
        $this->assertTrue(self::KEY_DELETED == $this->deleteOrFail("key1"));
    }

    public function test_function_deleteOrFail_return_exception_if_key_not_exists()
    {
        $this->clean();
        $this->expectException(ExposeApiInvalidArgument::class);
        $this->expectExceptionCode(404);

        $this->deleteOrFail("key1");
    }

    public function test_rating_append_function_will_append_value_as_rates_to_custom_key()
    {
        $this->clean();
        $this->save('key1', 'value1');

        $customPostfix = '-rate';

        $result = $this->append("key1", "rate1", $customPostfix);
        $result = $this->append("key1", "rate2", $customPostfix);

        $expected = '{"rates":["rate1","rate2"]}';

        $this->assertSame($expected, $result);
    }

    public function test_rating_append_function_will_fail_if_key_does_not_exists()
    {
        $this->expectException(ExposeApiInvalidArgument::class);

        $this->clean();
        $this->append("key1", "rate2", '-rate');
    }


}