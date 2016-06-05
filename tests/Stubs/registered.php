<?php

return collect([
    'test_settings1' => [
        'allowed' => ['bananas', 'grapes', 8, 'monkey'],
        'default' => 'monkey'
    ],
    'test_settings2' => [
        'allowed' => [true, false],
        'default' => true
    ]
]);
