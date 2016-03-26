<?php

namespace spec\ExampleBank\Domain;

use ExampleBank\Domain\AccountNumber;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AccountNumberSpec extends ObjectBehavior
{
    function it_must_be_created_from_string()
    {
        $this->beConstructedThrough('fromString', [123]);
        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }

    function it_is_equal_when_number_is_equal()
    {
        $this->beConstructedThrough('fromString', ['123ABC']);
        $this->equals(AccountNumber::fromString('123ABC'))->shouldReturn(true);
        $this->equals(AccountNumber::fromString('567DEF'))->shouldReturn(false);
    }

    function it_can_be_cast_to_string()
    {
        $this->beConstructedThrough('fromString', ['123ABC']);
        $this->__toString()->shouldReturn('123ABC');
    }
}
