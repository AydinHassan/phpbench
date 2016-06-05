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
class TimeResult implements ResultInterface
{
    /**
     * @var int
     */
    private $time;

    /**
     * @param mixed $time Time taken to execute the iteration in microseconds.
     */
    public function __construct($time)
    {
        $this->time = $time;
    }

    public function getTime()
    {
        return $this->time;
    }
}
