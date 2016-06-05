<?php

/*
 * This file is part of the PHPBench package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpBench\Tests\Unit\Benchmark\Executor;

use PhpBench\Benchmark\Executor;
use PhpBench\Benchmark\Executor\MicrotimeExecutor;
use PhpBench\Benchmark\Metadata\BenchmarkMetadata;
use PhpBench\Benchmark\Metadata\SubjectMetadata;
use PhpBench\Benchmark\Remote\Launcher;
use PhpBench\Model\Benchmark;
use PhpBench\Model\Iteration;
use PhpBench\Model\ParameterSet;
use PhpBench\Model\Variant;
use PhpBench\Registry\Config;
use PhpBench\Model\Result\TimeResult;
use PhpBench\Model\Result\MemoryResult;

class MicrotimeExecutorTest extends \PHPUnit_Framework_TestCase
{
    private $executor;
    private $beforeMethodFile;
    private $afterMethodFile;
    private $revFile;
    private $paramFile;
    private $paramBeforeFile;
    private $paramAfterFile;

    public function setUp()
    {
        $this->beforeMethodFile = __DIR__ . '/microtimetest/before_method.tmp';
        $this->afterMethodFile = __DIR__ . '/microtimetest/after_method.tmp';
        $this->staticMethodFile = __DIR__ . '/microtimetest/static_method.tmp';
        $this->revFile = __DIR__ . '/microtimetest/revs.tmp';
        $this->setupFile = __DIR__ . '/microtimetest/setup.tmp';
        $this->paramFile = __DIR__ . '/microtimetest/param.tmp';
        $this->paramBeforeFile = __DIR__ . '/microtimetest/parambefore.tmp';
        $this->paramAfterFile = __DIR__ . '/microtimetest/paramafter.tmp';
        $this->teardownFile = __DIR__ . '/microtimetest/teardown.tmp';

        $this->metadata = $this->prophesize(SubjectMetadata::class);
        $this->benchmark = $this->prophesize(Benchmark::class);
        $this->benchmarkMetadata = $this->prophesize(BenchmarkMetadata::class);
        $this->variant = $this->prophesize(Variant::class);

        $launcher = new Launcher(null, null);
        $this->executor = new MicrotimeExecutor($launcher);
        $this->removeTemporaryFiles();

        $this->benchmarkMetadata->getPath()->willReturn(__DIR__ . '/microtimetest/ExecutorBench.php');
        $this->benchmarkMetadata->getClass()->willReturn('PhpBench\Tests\Unit\Benchmark\Executor\microtimetest\ExecutorBench');
        $this->iteration = $this->prophesize(Iteration::class);
        $this->metadata->getBenchmark()->willReturn($this->benchmarkMetadata->reveal());
        $this->iteration->getVariant()->willReturn($this->variant->reveal());
    }

    public function tearDown()
    {
        $this->removeTemporaryFiles();
    }

    private function removeTemporaryFiles()
    {
        foreach ([
            $this->beforeMethodFile,
            $this->afterMethodFile,
            $this->revFile,
            $this->setupFile,
            $this->teardownFile,
            $this->paramFile,
            $this->paramBeforeFile,
            $this->paramAfterFile,
            $this->staticMethodFile,
        ] as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }

    /**
     * It should create a script which benchmarks the code and returns
     * the time taken and the memory used.
     */
    public function testExecute()
    {
        $this->metadata->getBeforeMethods()->willReturn([]);
        $this->metadata->getAfterMethods()->willReturn([]);
        $this->metadata->getName()->willReturn('doSomething');
        $this->variant->getParameterSet()->willReturn(new ParameterSet());
        $this->variant->getRevolutions()->willReturn(10);
        $this->variant->getWarmup()->willReturn(1);

        $results = $this->executor->execute(
            $this->metadata->reveal(),
            $this->iteration->reveal(),
            new Config('test', [])
        );

        $this->assertInstanceOf('PhpBench\Model\ResultCollection', $results);
        $this->assertInternalType('int', $results->getResult(TimeResult::class)->getTime());
        $this->assertInternalType('int', $results->getResult(MemoryResult::class)->getMemory());
        $this->assertFalse(file_exists($this->beforeMethodFile));
        $this->assertFalse(file_exists($this->afterMethodFile));
        $this->assertTrue(file_exists($this->revFile));

        // 10 revolutions + 1 warmup
        $this->assertEquals('11', file_get_contents($this->revFile));
    }

    /**
     * It should prevent output from the benchmarking class.
     *
     * @expectedException RuntimeException
     * @expectedException Benchmark made some noise
     */
    public function testRepressOutput()
    {
        $this->metadata->getBeforeMethods()->willReturn([]);
        $this->metadata->getAfterMethods()->willReturn([]);
        $this->metadata->getName()->willReturn('benchOutput');
        $this->metadata->getRevs()->willReturn(10);
        $this->metadata->getWarmup()->willReturn(0);
        $this->variant->getParameterSet()->willReturn(new ParameterSet());

        $results = $this->executor->execute($this->metadata->reveal(), $this->iteration->reveal(), new Config('test', []));

        $this->assertInstanceOf('PhpBench\Model\ResultCollection', $results);
    }

    /**
     * It should execute methods before the benchmark subject.
     */
    public function testExecuteBefore()
    {
        $this->metadata->getBeforeMethods()->willReturn(['beforeMethod']);
        $this->metadata->getAfterMethods()->willReturn([]);
        $this->metadata->getName()->willReturn('doSomething');
        $this->variant->getParameterSet()->willReturn(new ParameterSet());
        $this->variant->getRevolutions()->willReturn(1);
        $this->variant->getWarmup()->willReturn(0);

        $this->executor->execute($this->metadata->reveal(), $this->iteration->reveal(), new Config('test', []));

        $this->assertTrue(file_exists($this->beforeMethodFile));
    }

    /**
     * It should execute methods after the benchmark subject.
     */
    public function testExecuteAfter()
    {
        $this->metadata->getBeforeMethods()->willReturn([]);
        $this->metadata->getAfterMethods()->willReturn(['afterMethod']);
        $this->metadata->getName()->willReturn('doSomething');
        $this->variant->getParameterSet()->willReturn(new ParameterSet());
        $this->variant->getRevolutions()->willReturn(1);
        $this->variant->getWarmup()->willReturn(0);

        $this->executor->execute($this->metadata->reveal(), $this->iteration->reveal(), new Config('test', []));

        $this->assertTrue(file_exists($this->afterMethodFile));
    }

    /**
     * It should pass parameters to the benchmark method.
     */
    public function testParameters()
    {
        $this->metadata->getBeforeMethods()->willReturn([]);
        $this->metadata->getAfterMethods()->willReturn([]);
        $this->metadata->getName()->willReturn('parameterized');

        $this->variant->getParameterSet()->willReturn(new ParameterSet(0, [
            'one' => 'two',
            'three' => 'four',
        ]));
        $this->variant->getRevolutions()->willReturn(1);
        $this->variant->getWarmup()->willReturn(0);

        $this->executor->execute($this->metadata->reveal(), $this->iteration->reveal(), new Config('test', []));
        $this->assertTrue(file_exists($this->paramFile));
        $params = json_decode(file_get_contents($this->paramFile), true);
        $this->assertEquals([
            'one' => 'two',
            'three' => 'four',
        ], $params);
    }

    /**
     * It should pass parameters to the before metadata and after metadata methods.
     */
    public function testParametersBeforeSubject()
    {
        $expected = new ParameterSet(0, [
            'one' => 'two',
            'three' => 'four',
        ]);

        $this->metadata->getBeforeMethods()->willReturn(['parameterizedBefore']);
        $this->metadata->getAfterMethods()->willReturn(['parameterizedAfter']);
        $this->metadata->getName()->willReturn('parameterized');
        $this->variant->getParameterSet()->willReturn($expected);
        $this->variant->getRevolutions()->willReturn(1);
        $this->variant->getWarmup()->willReturn(0);

        $this->executor->execute($this->metadata->reveal(), $this->iteration->reveal(), new Config('test', []));

        $this->assertTrue(file_exists($this->paramBeforeFile));
        $params = json_decode(file_get_contents($this->paramBeforeFile), true);
        $this->assertEquals($expected->getArrayCopy(), $params);

        $this->assertTrue(file_exists($this->paramAfterFile));
        $params = json_decode(file_get_contents($this->paramAfterFile), true);
        $this->assertEquals($expected->getArrayCopy(), $params);
    }

    /**
     * It should execute arbitrary methods on the benchmark class.
     */
    public function testExecuteMethods()
    {
        $this->executor->executeMethods($this->benchmarkMetadata->reveal(), ['initDatabase']);
        $this->assertTrue(file_exists($this->staticMethodFile));
    }
}
