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

use PhpBench\Benchmark\Metadata\SubjectMetadata;
use PhpBench\Model\Benchmark;
use PhpBench\Model\Suite;

class BenchmarkTest extends \PHPUnit_Framework_TestCase
{
    private $benchmark;
    private $suite;

    public function setUp()
    {
        $this->suite = $this->prophesize(Suite::class);
        $this->benchmark = new Benchmark($this->suite->reveal(), '/path/to', 'Class');
    }

    /**
     * It should get and set .. things.
     */
    public function testGetSet()
    {
        $this->assertSame($this->suite->reveal(), $this->benchmark->getSuite());
    }

    /**
     * It should create and add a subject from some metadata.
     */
    public function testCreateSubjectFromMetadata()
    {
        $metadata = $this->prophesize(SubjectMetadata::class);
        $metadata->getName()->willReturn('hello');
        $metadata->getGroups()->willReturn(['one', 'two']);
        $metadata->getSleep()->willReturn(30);
        $metadata->getRetryThreshold()->willReturn(10);
        $metadata->getOutputTimeUnit()->willReturn(50);
        $metadata->getOutputMode()->willReturn(60);
        $metadata->getOutputTimePrecision()->willReturn(3);

        $subject = $this->benchmark->createSubjectFromMetadata($metadata->reveal());
        $this->assertInstanceOf('PhpBench\Model\Subject', $subject);
        $this->assertEquals('hello', $subject->getName());
        $this->assertEquals(['one', 'two'], $subject->getGroups());
        $this->assertEquals(30, $subject->getSleep());
        $this->assertEquals(10, $subject->getRetryThreshold());
        $this->assertEquals(50, $subject->getOutputTimeUnit());
        $this->assertEquals(60, $subject->getOutputMode());
        $this->assertEquals(3, $subject->getOutputTimePrecision());

        $subjects = $this->benchmark->getSubjects();
        $this->assertCount(1, $subjects);
        $bSubject = current($subjects);
        $this->assertInstanceOf('PhpBench\Model\Subject', $bSubject);
        $this->assertSame($subject, $bSubject);
    }
}
