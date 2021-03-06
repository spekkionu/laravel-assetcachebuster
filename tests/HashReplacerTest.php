<?php
namespace Spekkionu\Assetcachebuster\Tests;

use PHPUnit\Framework\TestCase;
use Spekkionu\Assetcachebuster\HashReplacer\ConfigHashReplacer;

require_once(dirname(__DIR__) . '/src/Spekkionu/Assetcachebuster/HashReplacer/HashReplacerInterface.php');
require_once(dirname(__DIR__) . '/src/Spekkionu/Assetcachebuster/HashReplacer/ConfigHashReplacer.php');

class HashReplacerTest extends TestCase
{
    public function testReplaceHash()
    {
        $oldHash = md5('old');
        $newHash = md5('new');
        $oldContent = "'hash' => '{$oldHash}',";
        $newContent =  "'hash' => '{$newHash}',";

        $cacheBuster = $this->getMockBuilder('\Spekkionu\Assetcachebuster\Assetcachebuster')->disableOriginalConstructor()->setMethods(['getHash', 'generateHash', 'setHash'])->getMock();
        $cacheBuster->method('getHash')->willReturn($oldHash);
        $cacheBuster->method('generateHash')->willReturn($newHash);
        $cacheBuster->expects($this->once())->method('setHash')->with($this->equalTo($newHash));

        $writer = $this->getMockBuilder('\Spekkionu\Assetcachebuster\Writer\WriterInterface')->setMethods(['getCurrentConfig', 'setCurrentConfig'])->getMock();
        $writer->method('getCurrentConfig')->willReturn($oldContent);
        $writer->expects($this->once())->method('setCurrentConfig')->with($this->equalTo($newContent));

        $replacer = new ConfigHashReplacer($cacheBuster, $writer);
        $replacer->replaceHash();
    }

	public function testBadConfig()
	{
		$oldHash = md5('old');
		$newHash = md5('new');
		$oldContent = "badconfig";
		$newContent =  "'hash' => '{$newHash}',";

		$cacheBuster = $this->getMockBuilder('\Spekkionu\Assetcachebuster\Assetcachebuster')->disableOriginalConstructor()->setMethods(['getHash', 'generateHash', 'setHash'])->getMock();
		$cacheBuster->method('getHash')->willReturn($oldHash);
		$cacheBuster->method('generateHash')->willReturn($newHash);
		$cacheBuster->expects($this->never())->method('setHash')->with($this->equalTo($newHash));

		$writer = $this->getMockBuilder('\Spekkionu\Assetcachebuster\Writer\WriterInterface')->setMethods(['getCurrentConfig', 'setCurrentConfig'])->getMock();
		$writer->method('getCurrentConfig')->willReturn($oldContent);
		$this->expectException(\RuntimeException::class);
		$writer->expects($this->never())->method('setCurrentConfig')->with($this->equalTo($newContent));

		$replacer = new ConfigHashReplacer($cacheBuster, $writer);
		$replacer->replaceHash();
    }
}
