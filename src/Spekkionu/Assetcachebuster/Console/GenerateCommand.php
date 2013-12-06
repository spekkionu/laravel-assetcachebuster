<?php namespace Spekkionu\Assetcachebuster\Console;


use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Filesystem\FileNotFoundException;

/**
 * Generates a new asset cache hash
 */
class GenerateCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'assetcachebuster:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates a new asset hash';


    /**
     * Create a new key generator command.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $this->line('Generating new asset hash. Environment: <comment>'.$this->laravel->make('env').'</comment>');

        list($path, $contents) = $this->getConfigFile();

        if (!$path) {
            return;
        }

        $hash = $this->generateHash();

        $contents = $this->replaceHash($hash, $contents);

        $this->files->put($path, $contents);

        $this->laravel['config']['assetcachebuster::hash'] = $hash;

        $msg = "New hash {$hash} generated.";
        $this->info($msg);
    }

    /**
     * Get the key file and contents.
     *
     * @return array
     */
    protected function getConfigFile()
    {
        $env = $this->option('env') ? $this->option('env').'/' : '';
        try {
            $contents = $this->files->get($path = $this->laravel['path']."/config/packages/spekkionu/assetcachebuster/{$env}config.php");
            return array($path, $contents);
        } catch (FileNotFoundException $e) {
            $this->error("Assetcachebuster config file not found.");
            $this->info("Did you publish the cache config?");
            $this->info("Try running php artisan config:publish spekkionu/assetcachebuster ");
            throw new \Exception("Assetcachebuster config file not found.");
        }
    }

    /**
     * Generate a random key for the application.
     *
     * @return string
     */
    protected function generateHash()
    {
        return md5(time());
    }

    protected function replaceHash($hash, $content)
    {
        $current = $this->laravel['config']['assetcachebuster::hash'];
        $content = preg_replace("/([\'\"]hash[\'\"].+?[\'\"])(".preg_quote($current, '/').")([\'\"].*)/", "'hash' => '" . $hash . "',", $content, 1, $count);
        if ($count != 1) {
            throw new \Exception("Could not find current hash key in config file.");
        }
        return $content;
    }

}