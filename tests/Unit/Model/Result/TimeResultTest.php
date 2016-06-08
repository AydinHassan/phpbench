<?php

namespace PhpBench\Tests\Unit\Model\Result;

use PhpBench\Model\Result\TimeResult;

class TimeResultTest extends \PHPUnit_Framework_TestCase
{
    public function testGetters()
    {
        $result = new TimeResult(10);
        $this->assertEquals(10, $result->getTime());
    }
}
