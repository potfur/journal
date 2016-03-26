<?php

namespace ExampleBank\Bus\Assert;

trait AssertAmount
{
    protected function assertAmount($amount)
    {
        if (empty($amount) || !is_int($amount)) {
            throw new \InvalidArgumentException('Amount must be a non empty integer');
        }
    }
}
