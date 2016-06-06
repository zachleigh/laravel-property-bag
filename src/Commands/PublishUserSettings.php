<?php

namespace LaravelPropertyBag\Commands;

use File;
use Illuminate\Console\Command;
use \Illuminate\Console\AppNamespaceDetectorTrait;

class PublishUserSettings extends Command
{
    use AppNamespaceDetectorTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lpb:publish-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish the user settings files to the app';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            File::makeDirectory(app_path('UserSettings'));
        } catch (\ErrorException $e) {
            $this->error('Folder UserSettings already exists! Abort! Abort!');

            return;
        }

        $namespace = $this->getAppNamespace().'UserSettings';

        $this->writePropertyBag($namespace);

        $this->writeSettings($namespace);

        $this->info('User settings files successfully created!');
    }

    /**
     * Write the property bag file into the settings folder.
     *
     * @param  string $namespace
     */
    protected function writePropertyBag($namespace)
    {
        $propertyBag = file_get_contents(
            __DIR__.'/../UserSettings/UserPropertyBag.php'
        );

        $propertyBag = $this->replaceNamespace($namespace, $propertyBag);

        file_put_contents(
            app_path('UserSettings/UserPropertyBag.php'),
            $propertyBag
        );
    }

    /**
     * Write the settings file into the settings folder.
     *
     * @param  string $namespace
     */
    protected function writeSettings($namespace)
    {
        $settings = file_get_contents(
            __DIR__.'/../UserSettings/UserSettings.php'
        );

        $settings = $this->replaceNamespace($namespace, $settings);

        file_put_contents(
            app_path('UserSettings/UserSettings.php'),
            $settings
        );
    }

    /**
     * Replace the default namespace with the app namespace.
     *
     * @param  string $namespace
     * @param  string $file
     *
     * @return string
     */
    protected function replaceNamespace($namespace, $file)
    {
        return str_replace(
            'LaravelPropertyBag\UserSettings',
            $namespace,
            $file
        );
    }
}
