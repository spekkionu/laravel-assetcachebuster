<?php namespace Spekkionu\Assetcachebuster;

use InvalidArgumentException;

class Assetcachebuster
{

    /**
     * Flag for if the package is anabled
     *
     * @var boolean $enabled
     */
    protected $enabled = false;

    /**
     * CDN Url
     *
     * @var string $cdn The url for the cdn
     */
    protected $cdn = '/';

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
     * @param array $options Array of options from the config file
     */
    public function __construct(array $options)
    {
        if (isset($options['enable'])) {
            $this->setEnabled($options['enable']);
        }
        if (isset($options['hash'])) {
            $this->setHash($options['hash']);
        }
        if (isset($options['prefix'])) {
            $this->setPrefix($options['prefix']);
        }
        if (isset($options['cdn'])) {
            $this->setCdnUrl($options['cdn']);
        }
    }

    /**
     * Enables / Disables the package
     *
     * @param boolean $enabled True to enable, false to disable
     */
    public function setEnabled($enabled = true)
    {
        $this->enabled = (bool) $enabled;
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

        $this->hash = $hash;
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
        if ($this->enabled) {
            return $this->cdn . $this->prefix . $path . '?' . $this->hash ;
        } else {
            return $this->cdn . $path;
        }

    }

    /**
     * Generate a random key for the application.
     *
     * @return string
     */
    public static function generateHash()
    {
        return \md5(\time());
    }
}
