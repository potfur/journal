<?php

/*
* This file is part of the journal package
*
* (c) Michal Wachowski <wachowski.michal@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Journal\Contract;

class ContractTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateFromObject()
    {
        $identifier = Contract::fromObject(new static);
        $this->assertEquals('journal.contract.contract_test', (string) $identifier);
    }

    public function testCreateFromClass()
    {
        $identifier = Contract::fromClass(static::class);
        $this->assertEquals('journal.contract.contract_test', (string) $identifier);
    }

    public function testCreateFromString()
    {
        $identifier = Contract::fromString('journal.contract.contract_test');
        $this->assertEquals('journal.contract.contract_test', (string) $identifier);
    }

    public function testCreateFromStringMayOnlyContainCertainCharacters()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Contract string must match /^[a-z0-9_.]+$/, got FooBar.');

        Contract::fromString('FooBar');
    }

    public function testIsEqual()
    {
        $identifier = Contract::fromString('journal.contract.contract_test');
        $this->assertTrue($identifier->equals(Contract::fromString('journal.contract.contract_test')));
    }

    public function testIsNotEqual()
    {
        $identifier = Contract::fromString('journal.contract.contract_test');
        $this->assertFalse($identifier->equals(Contract::fromString('reflection_class')));
    }

    public function testToClassName()
    {
        $identifier = Contract::fromClass(static::class);
        $this->assertEquals(static::class, $identifier->toClassName());
    }
}
