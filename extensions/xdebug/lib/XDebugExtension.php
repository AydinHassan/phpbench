<?php

/*
 * This file is part of the PHPBench package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpBench\Extensions\XDebug;

use PhpBench\DependencyInjection\Container;
use PhpBench\DependencyInjection\ExtensionInterface;
use PhpBench\Extensions\XDebug\Command\ProfileCommand;
use PhpBench\Extensions\XDebug\Executor\XDebugExecutor;

class XDebugExtension implements ExtensionInterface
{
    public function getDefaultConfig()
    {
        return [];
    }

    public function load(Container $container)
    {
        $container->register('xdebug.command.profile', function (Container $container) {
            return new ProfileCommand(
                $container->get('console.command.handler.runner')
            );
        }, ['console.command' => []]);

        $container->register('benchmark.executor.xdebug',
            function (Container $container) {
                return new XDebugExecutor(
                    $container->get('benchmark.remote.launcher')
                );
            },
            ['benchmark_executor' => ['name' => 'xdebug'],
        ]);
    }

    public function build(Container $container)
    {
    }
}
