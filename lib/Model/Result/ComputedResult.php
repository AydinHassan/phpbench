<?php

namespace PhpBench\Model\Result;

use PhpBench\Model\ResultInterface;

class ComputedResult implements ResultInterface
{
    private $zValue;
    private $deviation;
    private $rejectCount;

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
}
