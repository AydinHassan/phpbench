<?php

/*
 * This file is part of the PHPBench package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpBench\Extensions\XDebug\Executor;

use PhpBench\Benchmark\Executor\BaseExecutor;
use PhpBench\Benchmark\Remote\Payload;
use PhpBench\Extensions\XDebug\XDebugUtil;
use PhpBench\Model\Iteration;
use PhpBench\Model\IterationResult;
use PhpBench\PhpBench;
use PhpBench\Registry\Config;

class ProfileExecutor extends BaseExecutor
{
    /**
     * {@inheritdoc}
     */
    public function launch(Payload $payload, Iteration $iteration, Config $config)
    {
        $outputDir = $config['output_dir'];
        $callback = $config['callback'];
        $name = XDebugUtil::filenameFromIteration($iteration);

        $phpConfig = [
            'xdebug.trace_output_name' => $name,
            'xdebug.trace_output_dir' => $outputDir,
            'xdebug.trace_format' => '1',
            'xdebug.auto_trace' => '1',
            'xdebug.coverage_enable' => '0',
        ];

        if (!extension_loaded('xdebug')) {
            $phpConfig['zend_extension'] = 'xdebug.so';
        }

        $payload->setPhpConfig($phpConfig);
        $result = $payload->launch();

        if (isset($result['buffer']) && $result['buffer']) {
            throw new \RuntimeException(sprintf(
                'Benchmark made some noise: %s',
                $result['buffer']
            ));
        }

        $dom = $this->converter->convert($path);
        unlink($path);

        $class = $benchmark->getClass();
        if (substr($class, 0, 1) == '\\') {
            $class = substr($class, 1);
        }

        // extract only the timings for the benchmark class, ignore the bootstrapping
        $selector = '//entry[@function="' . $class . '->' . $subject->getName() . '"]';
        $time = $dom->evaluate('sum( ' . $selector . '/@end-time) - sum(' . $selector . '/@start-time)') * 1000000;
        $memory = $dom->evaluate('sum( ' . $selector . '/@end-memory) - sum(' . $selector . '/@start-memory)');
        $funcCalls = $dom->evaluate('count(' . $selector . '//*)');

        $result = new IterationResult(
            new Timeresult($time),
            new MemotyResult($memory)
        );

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultConfig()
    {
        return [
            'callback' => function () {
            },
            'output_dir' => 'profile',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getSchema()
    {
        return [
            'type' => 'object',
            'additionalProperties' => false,
            'properties' => [
                'callback' => [
                    'type' => null,
                ],
                'output_dir' => [
                    'type' => 'string',
                ],
            ],
        ];
    }
}
