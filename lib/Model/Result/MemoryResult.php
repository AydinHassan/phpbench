<?php

/*
 * This file is part of the PHPBench package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpBench\Model\Result;

use PhpBench\Model\ResultInterface;

/**
 * Represents the result of a single iteration executed by an executor.
 */
class MemoryResult implements ResultInterface
{
    /**
     * @var int
     */
    private $memory;

    /**
     * {@inheritdoc}
     */
    public static function fromArray(array $values)
    {
        return new self($values['memory']);
    }

    /**
     * @param mixed $memory Memory used by iteration in bytes.
     */
    public function __construct($memory)
    {
        $this->memory = $memory;
    }

    public function getMemory()
    {
        return $this->memory;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return [
            'memory' => $this->memory,
        ];
    }

    public function getKey()
    {
        return 'memory';
    }
}
