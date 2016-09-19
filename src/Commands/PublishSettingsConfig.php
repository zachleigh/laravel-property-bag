<?php

namespace LaravelPropertyBag\Commands;

use File;
use Illuminate\Console\Command;
use LaravelPropertyBag\Helpers\NameResolver;

class PublishSettingsConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pbag:make {resource}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a settings config file for a resource.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!File::exists(app_path('Settings'))) {
            File::makeDirectory(app_path('Settings'));
        }

        $namespace = NameResolver::getAppNamespace().'Settings';

        $resourceName = ucfirst($this->argument('resource'));

        $this->writeConfig($namespace, $resourceName);

        $this->info("{$resourceName} settings file successfully created!");
    }

    /**
     * Write the settings file into the settings folder.
     *
     * @param string $namespace
     * @param string $resourceName
     */
    protected function writeConfig($namespace, $resourceName)
    {
        $stub = file_get_contents(
            __DIR__.'/../Stubs/ResourceConfig.php'
        );

        $stub = $this->replace('{{Namespace}}', $namespace, $stub);

        $name = $resourceName.'Settings';

        $stub = $this->replace('{{ClassName}}', $name, $stub);

        file_put_contents(
            app_path("Settings/{$name}.php"),
            $stub
        );
    }

    /**
     * Replace mustache with replacement in file.
     *
     * @param string $mustache
     * @param string $replacement
     * @param string $file
     *
     * @return string
     */
    protected function replace($mustache, $replacement, $file)
    {
        return str_replace($mustache, $replacement, $file);
    }
}
