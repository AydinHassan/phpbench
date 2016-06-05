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

use PhpBench\Model\Result\ComputedResult;
use PhpBench\Model\Result\MemoryResult;
use PhpBench\Model\Result\TimeResult;

/**
 * Represents the data required to execute a single iteration.
 */
class Iteration
{
    private $variant;
    private $index;
    private $results;

    private $rejectionCount = 0;

    /**
     * @param int $index
     * @param int $revolutions
     */
    public function __construct(
        $index,
        Variant $variant,
        ResultCollection $results = null
    ) {
        $this->index = $index;
        $this->variant = $variant;
        $this->results = $results ?: new ResultCollection();
    }

    /**
     * Return the Variant that this
     * iteration belongs to.
     *
     * @return Variant
     */
    public function getVariant()
    {
        return $this->variant;
    }

    /**
     * Return the index of this iteration.
     *
     * @return int
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * Associate the result of the iteration with the iteration.
     *
     * TODO: Remove this method, just use the ResultCollection object available through ->getResults()
     *
     * @param int
     */
    public function setResults(ResultCollection $results)
    {
        foreach ($results as $result) {
            $this->results->addResult($result);
        }
    }

    public function getResults()
    {
        return $this->results;
    }

    /**
     * Return the deviation from the mean for this iteration.
     *
     * @return float
     */
    public function getDeviation()
    {
        return $this->results->getResult(ComputedResult::class)->getDeviation();
    }

    /**
     * Get the computed ZValue for this iteration.
     *
     * @return float
     */
    public function getZValue()
    {
        return $this->results->getResult(ComputedResult::class)->getZValue();
    }

    /**
     * Increase the reject count.
     */
    public function incrementRejectionCount()
    {
        $this->rejectionCount++;
    }

    /**
     * Return the number of times that this iteration was rejected.
     *
     * @return int
     */
    public function getRejectionCount()
    {
        return $this->results->hasResult(ComputedResult::class) ? $this->results->getResult(ComputedResult::class)->getRejectCount() : 0;
    }

    /**
     * Return the time taken (in microseconds) to perform this iteration (or
     * NULL if not yet performed.
     *
     * TODO: Remove this method
     *
     * @return int
     */
    public function getTime()
    {
        return $this->results->getResult(TimeResult::class)->getTime();
    }

    /**
     * Return the memory (in bytes) taken to perform this iteration (or
     * NULL if not yet performed.
     *
     * TODO: Remove this method.
     *
     * @return int
     */
    public function getMemory()
    {
        return $this->results->hasResult(MemoryResult::class) ? $this->results->getResult(MemoryResult::class)->getMemory() : null;
    }

    /**
     * Return the revolution time.
     *
     * TODO: Remove this method.
     *
     * @return float
     */
    public function getRevTime()
    {
        return $this->getTime() / $this->getVariant()->getRevolutions();
    }
}
