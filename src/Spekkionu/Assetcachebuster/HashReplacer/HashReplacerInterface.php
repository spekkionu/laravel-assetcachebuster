<?php
namespace Spekkionu\Assetcachebuster\HashReplacer;

interface HashReplacerInterface
{

    /**
     * Generate and save new hash
     * @return string
     */
    public function replaceHash();

    /**
     * @param string $oldHash
     * @param string $newHash
     * @return string New Hash
     */
    public function writeHash($oldHash, $newHash);
}
