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

interface Identifier
{
    /**
     * Create identifier from string
     *
     * @param string $string
     *
     * @return static
     */
    public static function fromString($string);

    /**
     * Cast identifier to string
     *
     * @return string
     */
    public function __toString();

    /**
     * Compare two identifiers
     *
     * @param Identifier $other
     *
     * @return bool
     */
    public function equals(Identifier $other);
}
