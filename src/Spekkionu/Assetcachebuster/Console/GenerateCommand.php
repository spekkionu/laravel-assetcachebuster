<?php namespace Spekkionu\Assetcachebuster\Console;


use Illuminate\Console\Command;
use Illuminate\Contracts\Config\Repository as Config;
use Spekkionu\Assetcachebuster\HashReplacer\HashReplacerInterface;

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
     * @var HashReplacerInterface
     */
    private $hashReplacer;
    /**
     * @var Config
     */
    private $config;


    /**
     * Create a new key generator command.
     *
     * @param HashReplacerInterface $hashReplacer
     * @param Config $config
     */
    public function __construct(HashReplacerInterface $hashReplacer, Config $config)
    {
        parent::__construct();

        $this->hashReplacer = $hashReplacer;
        $this->config = $config;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $this->line('Generating new asset hash. Environment: <comment>'.$this->laravel->make('env').'</comment>');

        $hash = $this->hashReplacer->replaceHash();

        $this->config->set('assetcachebuster.hash', $hash);

        $msg = "New hash {$hash} generated.";
        $this->info($msg);
    }
}
