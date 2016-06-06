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

class ComputedResult implements ResultInterface
{
    private $zValue;
    private $deviation;
    private $rejectCount;

    /**
     * {@inheritdoc}
     */
    public static function fromArray(array $values)
    {
        return new self(
            $values['z_value'],
            $values['deviation'],
            $values['rejection_count']
        );
    }

    public function __construct($zValue, $deviation, $rejectCount)
    {
        $this->zValue = $zValue;
        $this->deviation = $deviation;
        $this->rejectCount = $rejectCount;
    }

    public function getZValue()
    {
        return $this->zValue;
    }

    public function getDeviation()
    {
        return $this->deviation;
    }

    public function getRejectCount()
    {
        return $this->rejectCount;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return [
            'z_value' => $this->zValue,
            'deviation' => $this->deviation,
            'rejection_count' => $this->rejectCount,
        ];
    }

    public function getKey()
    {
        return 'comp';
    }
}
