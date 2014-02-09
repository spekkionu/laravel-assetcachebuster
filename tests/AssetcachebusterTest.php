<?php namespace Spekkionu\Assetcachebuster\Tests;

use Spekkionu\Assetcachebuster\Assetcachebuster;
use PHPUnit_Framework_TestCase;

class AssetcachebusterTest extends PHPUnit_Framework_TestCase
{

    public function testSimpleGeneration()
    {
        $options = array(
            'enable' => true,
            'hash' => '5a7319578bf4d83d08f3be1d8913f6ab',
            'prefix' => '',
            'cdn' => ''
        );
        $path = '/styles/styles.css';
        $expected = "/{$options['hash']}{$path}";
        $cachebuster = new Assetcachebuster($options);
        $this->assertEquals($expected, $cachebuster->url($path));
    }

    public function testPrefixed()
    {
        $options = array(
            'enable' => true,
            'hash' => '5a7319578bf4d83d08f3be1d8913f6ab',
            'prefix' => 'assets',
            'cdn' => ''
        );
        $path = '/styles/styles.css';
        $expected = "/{$options['prefix']}/{$options['hash']}{$path}";
        $cachebuster = new Assetcachebuster($options);
        $this->assertEquals($expected, $cachebuster->url($path));

    }

    public function testCdn()
    {
        $options = array(
            'enable' => true,
            'hash' => '5a7319578bf4d83d08f3be1d8913f6ab',
            'prefix' => '',
            'cdn' => 'http://cdn.static.com'
        );
        $path = '/styles/styles.css';
        $expected = "{$options['cdn']}/{$options['hash']}{$path}";
        $cachebuster = new Assetcachebuster($options);
        $this->assertEquals($expected, $cachebuster->url($path));

    }

    public function testPrefixedCdn()
    {
        $options = array(
            'enable' => true,
            'hash' => '5a7319578bf4d83d08f3be1d8913f6ab',
            'prefix' => 'assets',
            'cdn' => 'http://cdn.static.com'
        );
        $path = '/styles/styles.css';
        $expected = "{$options['cdn']}/{$options['prefix']}/{$options['hash']}{$path}";
        $cachebuster = new Assetcachebuster($options);
        $this->assertEquals($expected, $cachebuster->url($path));

    }

    public function testDisabled()
    {
        $options = array(
            'enable' => false,
            'hash' => '5a7319578bf4d83d08f3be1d8913f6ab',
            'prefix' => 'assets',
            'cdn' => 'http://cdn.static.com'
        );
        $path = '/styles/styles.css';
        $expected = "{$options['cdn']}{$path}";
        $cachebuster = new Assetcachebuster($options);
        $this->assertEquals($expected, $cachebuster->url($path));
    }

    public function getGenerateHash()
    {
        $hash = Assetcachebuster::generateHash();
        $this->assertEquals(32, strlen($hash));
    }
}
