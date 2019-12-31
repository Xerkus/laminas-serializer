<?php

/**
 * @see       https://github.com/laminas/laminas-serializer for the canonical source repository
 * @copyright https://github.com/laminas/laminas-serializer/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-serializer/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Serializer;

use Laminas\Serializer\Adapter;
use Laminas\Serializer\AdapterPluginManager;
use Laminas\Serializer\Exception\RuntimeException;
use Laminas\ServiceManager\ServiceManager;
use Laminas\ServiceManager\Test\CommonPluginManagerTrait;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

class AdapterPluginManagerCompatibilityTest extends TestCase
{
    use CommonPluginManagerTrait;

    protected function getPluginManager()
    {
        return new AdapterPluginManager(new ServiceManager());
    }

    protected function getV2InvalidPluginException()
    {
        return RuntimeException::class;
    }

    protected function getInstanceOf()
    {
        return Adapter\AdapterInterface::class;
    }

    /**
     * Overrides CommonPluginManagerTrait::aliasProvider
     *
     * Iterates through aliases, and for adapters that require extensions,
     * tests if the extension is loaded, skipping that alias if not.
     *
     * @return \Traversable
     */
    public function aliasProvider()
    {
        $pluginManager = $this->getPluginManager();
        $r = new ReflectionProperty($pluginManager, 'aliases');
        $r->setAccessible(true);
        $aliases = $r->getValue($pluginManager);

        foreach ($aliases as $alias => $target) {
            switch ($target) {
                case Adapter\IgBinary::class:
                    if (extension_loaded('igbinary')) {
                        yield $alias => [$alias, $target];
                    }
                    break;
                case Adapter\MsgPack::class:
                    if (extension_loaded('msgpack')) {
                        yield $alias => [$alias, $target];
                    }
                    break;
                case Adapter\Wddx::class:
                    if (extension_loaded('wddx')) {
                        yield $alias => [$alias, $target];
                    }
                    break;
                default:
                    yield $alias => [$alias, $target];
            }
        }
    }
}
