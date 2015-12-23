<?php
namespace Spekkionu\Assetcachebuster\Tests;

use Spekkionu\Assetcachebuster\Writer\ConfigWriter;
use PHPUnit_Framework_TestCase;

require_once(dirname(__DIR__) . '/src/Spekkionu\Assetcachebuster\Writer\WriterInterface.php');
require_once(dirname(__DIR__) . '/src/Spekkionu\Assetcachebuster\Writer\ConfigWriter.php');

class ConfigWriterTest extends PHPUnit_Framework_TestCase
{
    public function testGetCurrentConfig()
    {
        $dir = __DIR__ . DIRECTORY_SEPARATOR . '_data';
        $path = $dir . DIRECTORY_SEPARATOR . "assetcachebuster.php";
        $filesystem = $this->getMockBuilder('\Illuminate\Filesystem\Filesystem')->setMethods(['get'])->getMock();
        $filesystem->expects($this->once())->method('get')->with($this->equalTo($path));

        $writer = new ConfigWriter($filesystem, $dir);
        $writer->getCurrentConfig();

    }

    public function testSetCurrentConfig()
    {
        $content = 'config file test content';
        $dir = __DIR__ . DIRECTORY_SEPARATOR . '_data';
        $path = $dir . DIRECTORY_SEPARATOR . "assetcachebuster.php";
        $filesystem = $this->getMockBuilder('\Illuminate\Filesystem\Filesystem')->setMethods(['put'])->getMock();
        $filesystem->expects($this->once())->method('put')->with($this->equalTo($path), $this->equalTo($content));

        $writer = new ConfigWriter($filesystem, $dir);
        $writer->setCurrentConfig($content);
    }
}
