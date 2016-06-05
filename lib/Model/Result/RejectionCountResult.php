<?php

namespace PhpBench\Model\Result;

use PhpBench\Model\ResultInterface;

class RejectionCountResult implements ResultInterface
{
    private $rejectionCount;

    public function __construct($rejectionCount)
    {
        $this->rejectionCount = (int) $rejectionCount;
    }

    public function getRejectionCount() 
    {
        return $this->rejectionCount;
    }
}
