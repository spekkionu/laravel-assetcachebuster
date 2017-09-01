<?php
namespace Spekkionu\Assetcachebuster\HashReplacer;

use Spekkionu\Assetcachebuster\Writer\WriterInterface;
use Spekkionu\Assetcachebuster\Assetcachebuster as CacheBuster;

class ConfigHashReplacer implements HashReplacerInterface
{
    /**
     * @var WriterInterface
     */
    private $writer;

    /**
     * @var CacheBuster
     */
    private $cacheBuster;

    /**
     * HashReplacer constructor.
     * @param CacheBuster $cacheBuster
     * @param WriterInterface $writer
     */
    public function __construct(CacheBuster $cacheBuster, WriterInterface $writer)
    {
        $this->writer = $writer;
        $this->cacheBuster = $cacheBuster;
    }

    /**
     * Generate and save new hash
     * @return string New Hash
     * @throws \Exception
     */
    public function replaceHash()
    {
        $currentHash = $this->cacheBuster->getHash();
        $hash = $this->cacheBuster->generateHash();
        $this->writeHash($currentHash, $hash);
        $this->cacheBuster->setHash($hash);
        return $hash;
    }

    /**
     * @return string New hash
     * @throws \Exception
     */
    public function writeHash($currentHash, $hash)
    {
        $content = $this->writer->getCurrentConfig();
        $content = preg_replace(
            "/([\'\"]hash[\'\"].+?[\'\"])(" . preg_quote($currentHash, '/') . ")([\'\"].*)/",
            "'hash' => '" . $hash . "',",
            $content,
            1,
            $count
        );
        if ($count != 1) {
            throw new \RuntimeException("Could not find current hash key in config.");
        }

        $this->writer->setCurrentConfig($content);
        return $hash;
    }
}
