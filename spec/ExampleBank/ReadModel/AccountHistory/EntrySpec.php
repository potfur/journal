<?php

namespace spec\ExampleBank\ReadModel\AccountHistory;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class EntrySpec extends ObjectBehavior
{
    function it_must_be_created_with_valid_timestamp()
    {
        $this->beConstructedWith('abc', 100, 'description');
        $this->shouldThrow(\Exception::class)->duringInstantiation();
    }

    function it_must_be_created_with_integer_amount()
    {
        $this->beConstructedWith(1458077872, 'abc', 'description');
        $this->shouldThrow(\Exception::class)->duringInstantiation();
    }

    function it_has_description()
    {
        $this->beConstructedWith(1458077872, 100, 'description');
        $this->getDescription()->shouldReturn('description');
    }

    function it_has_amount()
    {
        $this->beConstructedWith(1458077872, 100, 'description');
        $this->getAmount()->shouldReturn(100);
    }

    function it_has_occurrence_date()
    {
        $this->beConstructedWith(1458077872, 100, 'description');
        $this->getDate()->format('Y-m-d H:i:s')->shouldReturn('2016-03-15 21:37:52');
    }
}
