<?php

namespace LaravelPropertyBag\Commands;

use File;
use Illuminate\Console\Command;

class PbagCommand extends Command
{
    /**
     * Make directory if it doesn't already exist.
     *
     * @param string $dir
     */
    protected function makeDir($dir)
    {
        if (!File::exists(app_path($dir))) {
            File::makeDirectory(app_path($dir));
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
