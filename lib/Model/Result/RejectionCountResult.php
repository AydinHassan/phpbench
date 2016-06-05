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
