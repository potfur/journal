<?php

namespace ExampleBank\Bus\Assert;

trait AssertAccountNumber
{
    protected function assertAccountNumber($accountNumber)
    {
        if (empty($accountNumber) || !is_string($accountNumber)) {
            throw new \InvalidArgumentException('Account number must be a non empty string');
        }
    }
}
