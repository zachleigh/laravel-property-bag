<?php

namespace App\Settings\Resources;

class Rules
{
    public static function ruleExample($value)
    {
        if ($value === 1) {
            return true;
        }

        return false;
    }
}
