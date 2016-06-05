<?php

/*
 * This file is part of the PHPBench package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpBench\Tests\Unit\Model;

use PhpBench\Model\Result\MemoryResult;
use PhpBench\Model\Result\TimeResult;
use PhpBench\Model\ResultCollection;

class ResultCollectionTest extends \PHPUnit_Framework_TestCase
{
    private $collection;

    public function setUp()
    {
        $this->timeResult = new TimeResult(1);
        $this->memoryResult = new MemoryResult(1);

        $this->collection = new ResultCollection([]);
    }

    /**
     * It can have results added in the constructor.
     */
    public function testAddConstructor()
    {
        $collection = new ResultCollection([
            $expected = new TimeResult(10),
        ]);

        $result = $collection->getResult(TimeResult::class);
        $this->assertSame($expected, $result);
    }

    /**
     * It should be able to have results added to it.
     * It should retrive results.
     */
    public function testAddResult()
    {
        $this->collection->addResult($this->timeResult);
        $this->assertEquals(
            $this->timeResult,
            $this->collection->getResult(TimeResult::class)
        );
    }

    /**
     * It should throw an exception if two results of the same class are added.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Result of class "PhpBench\Model\Result\TimeResult" has already been set.
     */
    public function testAddTwoResultsSameClass()
    {
        $this->collection->addResult($this->timeResult);
        $this->collection->addResult($this->timeResult);
    }

    /**
     * It should throw an exception when retrieving a non-existant class.
     *
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Result of class "stdClass" has not been set
     */
    public function testNonExistantClass()
    {
        $this->collection->getResult(\stdClass::class);
    }
}
