<?php
namespace Spekkionu\Assetcachebuster\Writer;

use Illuminate\Filesystem\Filesystem;

class ConfigWriter implements WriterInterface
{
    /**
     * @var Filesystem
     */
    private $filesystem;
    /**
     * @var
     */
    private $config_path;

    /**
     * ConfigWriter constructor.
     * @param Filesystem $filesystem
     * @param string $config_path
     */
    public function __construct(Filesystem $filesystem, $config_path)
    {
        $this->filesystem = $filesystem;
        $this->config_path = $config_path;
    }

    /**
     * Get the key file and contents.
     *
     * @return array
     */
    protected function getConfigFile()
    {
        return $this->config_path . DIRECTORY_SEPARATOR . "assetcachebuster.php";
    }

    /**
     * Check if the config file exists
     * 
     * @return bool
     */
    protected function configExists()
    {
        return $this->filesystem->exists($this->getConfigFile());
    }

    /**
     * Returns current config as string
     *
     * @return string
     */
    public function getCurrentConfig()
    {
        if (!$this->configExists()) {
            throw new \InvalidArgumentException('The config file does not exist. Did you run the vendor:publish command?');
        }
        return $this->filesystem->get($this->getConfigFile());
    }

    /**
     * Saves config as current config
     * @param string $content
     * @return void
     */
    public function setCurrentConfig($content)
    {
        if (!$this->configExists()) {
            throw new \InvalidArgumentException('The config file does not exist. Did you run the vendor:publish command?');
        }
        $this->filesystem->put($this->getConfigFile(), $content);
    }
}
