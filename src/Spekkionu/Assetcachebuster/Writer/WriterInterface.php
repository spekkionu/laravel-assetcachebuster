<?php
namespace Spekkionu\Assetcachebuster\Writer;


interface WriterInterface
{
    /**
     * Returns current config as string
     * @return string
     */
    public function getCurrentConfig();

    /**
     * Saves config as current config
     * @param string $content
     * @return void
     */
    public function setCurrentConfig($content);
}
