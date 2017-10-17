<?php
namespace Spekkionu\Assetcachebuster\Tests;

use PHPUnit\Framework\TestCase;
use Spekkionu\Assetcachebuster\Writer\ConfigWriter;

require_once(dirname(__DIR__) . '/src/Spekkionu/Assetcachebuster/Writer/WriterInterface.php');
require_once(dirname(__DIR__) . '/src/Spekkionu/Assetcachebuster/Writer/ConfigWriter.php');

class ConfigWriterTest extends TestCase
{
    public function testGetCurrentConfig()
    {
        $dir = __DIR__ . DIRECTORY_SEPARATOR . '_data';
        $path = $dir . DIRECTORY_SEPARATOR . "assetcachebuster.php";
        $filesystem = $this->getMockBuilder('\Illuminate\Filesystem\Filesystem')->setMethods(['get', 'exists'])->getMock();
        $filesystem->expects($this->once())->method('exists')->will($this->returnValue(true));
        $filesystem->expects($this->once())->method('get')->with($this->equalTo($path));

        $writer = new ConfigWriter($filesystem, $dir);
        $writer->getCurrentConfig();

    }

    public function testSetCurrentConfig()
    {
        $content = 'config file test content';
        $dir = __DIR__ . DIRECTORY_SEPARATOR . '_data';
        $path = $dir . DIRECTORY_SEPARATOR . "assetcachebuster.php";
        $filesystem = $this->getMockBuilder('\Illuminate\Filesystem\Filesystem')->setMethods(['put', 'exists'])->getMock();
        $filesystem->expects($this->once())->method('exists')->will($this->returnValue(true));
        $filesystem->expects($this->once())->method('put')->with($this->equalTo($path), $this->equalTo($content));

        $writer = new ConfigWriter($filesystem, $dir);
        $writer->setCurrentConfig($content);
    }

    public function testGetWithoutConfigFile()
    {
        $content = 'config file test content';
        $dir = __DIR__ . DIRECTORY_SEPARATOR . '_data';
        $path = $dir . DIRECTORY_SEPARATOR . "assetcachebuster.php";
        $filesystem = $this->getMockBuilder('\Illuminate\Filesystem\Filesystem')->setMethods(['get', 'exists'])->getMock();
        $filesystem->expects($this->once())->method('exists')->will($this->returnValue(false));
        $filesystem->expects($this->never())->method('get');

        $this->expectException(\InvalidArgumentException::class);
        $writer = new ConfigWriter($filesystem, $dir);
        $writer->getCurrentConfig($content);
    }

    public function testSetWithoutConfigFile()
    {
        $content = 'config file test content';
        $dir = __DIR__ . DIRECTORY_SEPARATOR . '_data';
        $path = $dir . DIRECTORY_SEPARATOR . "assetcachebuster.php";
        $filesystem = $this->getMockBuilder('\Illuminate\Filesystem\Filesystem')->setMethods(['put', 'exists'])->getMock();
        $filesystem->expects($this->once())->method('exists')->will($this->returnValue(false));
        $filesystem->expects($this->never())->method('put');

        $this->expectException(\InvalidArgumentException::class);
        $writer = new ConfigWriter($filesystem, $dir);
        $writer->setCurrentConfig($content);
    }
}
