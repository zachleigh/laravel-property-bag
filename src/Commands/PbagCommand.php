<?php

namespace LaravelPropertyBag\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PbagCommand extends Command
{
    /**
     * Make directory if it doesn't already exist.
     *
     * @param string $dir
     */
    protected function makeDir($dir)
    {
        $dirPath = base_path('app/'.ltrim($dir, '/'));

        if (!File::exists($dirPath)) {
            File::makeDirectory($dirPath);
        }
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
