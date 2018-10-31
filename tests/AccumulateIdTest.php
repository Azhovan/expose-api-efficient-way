<?php

namespace Tests;

use App\ExposeApi\Core\AccumulateId;
use App\ExposeApi\Core\AccumulateIdInterface;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

class AccumulateIdTest extends TestCase
{
    /**
     * @expectedException \TypeError
     */
    public function test_AccumulateId_constructor_instance_of_UuidInterface()
    {
        new AccumulateId('');
    }

    public function test_generate_function_return_36_length_string()
    {
        $interfaceMock = $this->createMock(UuidInterface::class);
        $accId = new AccumulateId($interfaceMock);
        $hash = $accId::generate();

        $this->assertTrue(strlen($hash) == 36);
    }

    public function test_rebuild_function_should_return_AccumulateIdInterface_instance()
    {
        $uuid = '4df604aa-0693-4d8e-ad26-6f3eca4d81b6';

        $interfaceMock = $this->createMock(UuidInterface::class);
        $accId = new AccumulateId($interfaceMock);

        $this->assertTrue($accId::rebuild($uuid) instanceof AccumulateIdInterface);
    }

    public function test_generate_function_return_UuidInterface_instance()
    {
        $interfaceMock = $this->createMock(UuidInterface::class);
        $accId = new AccumulateId($interfaceMock);

        $this->assertTrue($accId::generate() instanceof UuidInterface);
    }

    public function test_toString_magic_method_implemented()
    {
        $interfaceMock = $this->createMock(UuidInterface::class);
        $accObject = new AccumulateId($interfaceMock);

        $this->assertTrue(method_exists($accObject, '__toString'));
    }
}
