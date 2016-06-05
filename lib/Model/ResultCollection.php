<?php

/*
 * This file is part of the PHPBench package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpBench\Model;

/**
 * Represents the result of a single iteration executed by an executor.
 */
class ResultCollection implements \IteratorAggregate
{
    private $results;

    public function __construct(array $results = [])
    {
        foreach ($results as $result) {
            $this->addResult($result);
        }
    }

    public function addResult(ResultInterface $result)
    {
        $class = get_class($result);

        if (isset($this->results[$class])) {
            throw new \InvalidArgumentException(sprintf(
                'Result of class "%s" has already been set.',
                $class
            ));
        }

        return $this->results[$class] = $result;
    }

    public function replaceResult(ResultInterface $result)
    {
        $this->results[get_class($result)] = $result;
    }

    public function hasResult($class)
    {
        return isset($this->results[$class]);
    }

    public function getResult($class)
    {
        if (!isset($this->results[$class])) {
            throw new \RuntimeException(sprintf(
                'Result of class "%s" has not been set',
                $class
            ));
        }

        return $this->results[$class];
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->results);
    }
}
