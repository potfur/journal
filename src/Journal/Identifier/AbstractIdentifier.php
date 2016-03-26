<?php

/*
* This file is part of the journal package
*
* (c) Michal Wachowski <wachowski.michal@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Journal\Identifier;

abstract class AbstractIdentifier
{
    private $id;

    private function __construct($identifier)
    {
        $this->id = $identifier;
    }

    /**
     * {@inheritdoc}
     */
    public static function fromString($string)
    {
        return new static((string) $string);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return (string) $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function equals(Identifier $other)
    {
        return (string) $this == (string) $other;
    }
}
