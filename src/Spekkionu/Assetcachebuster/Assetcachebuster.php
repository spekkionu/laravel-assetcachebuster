<?php namespace Spekkionu\Assetcachebuster;

use InvalidArgumentException;

class Assetcachebuster {

    /**
     * CDN Url
     *
     * @var string $cdn The url for the cdn
     */
    protected $cdn = null;

    /**
     * Asset cache busting hash
     *
     * @var string $hash The hash to use to bust the cache
     */
    protected $hash = null;

    /**
     * Asset prefix
     *
     * @var string $prefix A prefix containing assets
     */
    protected $prefix = null;

    /**
     * Class constructor
     *
     * @param string $hash The hash to use to bust the cache
     * @param string $prefix A prefix containing assets
     * @param string $cdn The url for the cdn
     */
    public function __construct($hash, $prefix = null, $cdn = null)
    {
        $this->setHash($hash);
        $this->setPrefix($prefix);
        $this->setCdnUrl($cdn);
    }

    /**
     * Sets the hash
     *
     * @param string $hash The hash to use to bust the cache
     */
    public function setHash($hash)
    {
        if (!preg_match("/[0-9a-f]{32}/", $hash)) {
            throw new InvalidArgumentException("Asset cache buster hash must be a valid md5 hash.");
        }
        $hash = trim($hash, '/');

        $this->hash = ($hash) ? trim($hash, '/') . '/' : '';
    }

    /**
     * Sets the asset prefix path
     *
     * @param string $prefix A prefix containing assets
     */
    public function setPrefix($prefix = null)
    {
        $prefix = trim($prefix, '/');

        $this->prefix = ($prefix) ? trim($prefix, '/') . '/' : '';
    }

    /**
     * Sets the CDN url
     *
     * @param string $cdn The url for the cdn
     */
    public function setCdnUrl($cdn = null)
    {
        $this->cdn = trim($cdn, '/') . '/';
    }

    /**
     * Generates an asset url
     *
     * @param string $path The path to the asset
     *
     * @return string The asset url with the cache busting hash
     */
    public function url($path = '')
    {
        $path = trim($path, '/');
        return $this->cdn . $this->prefix . $this->hash . $path;
    }
}
