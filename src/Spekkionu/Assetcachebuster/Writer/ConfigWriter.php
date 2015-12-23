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
     * Returns current config as string
     *
     * @return string
     */
    public function getCurrentConfig()
    {
        $path = $this->getConfigFile();
        return $this->filesystem->get($path);

    }

    /**
     * Saves config as current config
     * @param string $content
     * @return void
     */
    public function setCurrentConfig($content)
    {
        $path = $this->getConfigFile();
        $this->filesystem->put($path, $content);
    }


}
