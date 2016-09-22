<?php

namespace LaravelPropertyBag\Commands;

use File;
use LaravelPropertyBag\Helpers\NameResolver;

class PublishRulesFile extends PbagCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pbag:rules';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make user-defined rules file in Settings/Resources.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!File::exists(app_path('Settings'))) {
            File::makeDirectory(app_path('Settings'));
        }

        if (!File::exists(app_path('Settings/Resources'))) {
            File::makeDirectory(app_path('Settings/Resources'));
        }

        $namespace = NameResolver::getAppNamespace().'Settings\\Resources';

        $this->writeRulesFile($namespace);

        $this->info("Rules file successfully created!");
    }

    /**
     * Write the settings file into the settings folder.
     *
     * @param string $namespace
     */
    protected function writeRulesFile($namespace)
    {
        $stub = file_get_contents(
            __DIR__.'/../Stubs/Rules.php'
        );

        $stub = $this->replace('{{Namespace}}', $namespace, $stub);

        file_put_contents(
            app_path('Settings/Resources/Rules.php'),
            $stub
        );
    }
}
