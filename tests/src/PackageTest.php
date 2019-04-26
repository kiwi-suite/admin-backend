<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Test\Application;

use Ixocreate\Admin\AdminBootstrapItem;
use Ixocreate\Admin\ConfigProvider;
use Ixocreate\Admin\Package;
use Ixocreate\Application\Configurator\ConfiguratorRegistryInterface;
use Ixocreate\Application\Service\ServiceRegistryInterface;
use Ixocreate\ServiceManager\ServiceManagerInterface;
use PHPUnit\Framework\TestCase;

class PackageTest extends TestCase
{
    /**
     * @covers \Ixocreate\Admin\Package
     */
    public function testPackage()
    {
        $configuratorRegistry = $this->getMockBuilder(ConfiguratorRegistryInterface::class)->getMock();
        $serviceRegistry = $this->getMockBuilder(ServiceRegistryInterface::class)->getMock();
        $serviceManager = $this->getMockBuilder(ServiceManagerInterface::class)->getMock();

        $package = new Package();
        $package->configure($configuratorRegistry);
        $package->addServices($serviceRegistry);
        $package->boot($serviceManager);

        $this->assertSame([ConfigProvider::class], $package->getConfigProvider());
        $this->assertSame([AdminBootstrapItem::class], $package->getBootstrapItems());
        $this->assertDirectoryExists($package->getBootstrapDirectory());
        $this->assertNull($package->getConfigDirectory());
        $this->assertSame([
            \Ixocreate\Media\Package::class,
            \Ixocreate\Cms\Package::class,
            \Ixocreate\Intl\Package::class,
        ], $package->getDependencies());
    }
}
