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

final class Contract
{
    const REGEXP = '/^[a-z0-9_.]+$/';

    private $id;

    private function __construct($identifier)
    {
        $this->id = $identifier;
    }

    /**
     * Create contract from object instance
     *
     * @param object $object
     *
     * @return static
     */
    public static function fromObject($object)
    {
        return self::fromClass(get_class($object));
    }

    /**
     * Create contract from class name
     *
     * @param string $className
     *
     * @return static
     */
    public static function fromClass($className)
    {
        $className = preg_replace('/(?!^)(_?[A-Z]+)/', '_$1', $className);
        $className = strtolower($className);
        $className = str_replace(['\\_', '\\'], '.', $className);

        return self::fromString(ltrim($className, '_'));
    }

    /**
     * Create contract from string
     *
     * @param string $string
     *
     * @return static
     */
    public static function fromString($string)
    {
        if (!preg_match(self::REGEXP, $string)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Contract string must match %s, got %s.',
                    self::REGEXP,
                    $string
                )
            );
        }

        return new static((string) $string);
    }

    /**
     * Return contract as class name
     *
     * @return string
     */
    public function toClassName()
    {
        $className = preg_replace_callback(
            '/([\._])([a-z])/i',
            function ($match) {
                return ($match[1] === '.' ? '.' : '') . strtoupper($match[2]);
            },
            (string) $this
        );
        $className = ucfirst($className);
        $className = str_replace('.', '\\', $className);

        return $className;
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
    public function equals(Contract $other)
    {
        return (string) $this == (string) $other;
    }
}
