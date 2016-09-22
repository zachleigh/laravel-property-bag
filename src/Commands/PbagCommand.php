<?php

namespace LaravelPropertyBag\Commands;

use Illuminate\Console\Command;

class PbagCommand extends Command
{
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
