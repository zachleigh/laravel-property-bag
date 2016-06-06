<?php

return [
    'test_settings1' => [
        'allowed' => ['bananas', 'grapes', 8, 'monkey'],
        'default' => 'monkey'
    ],

    'test_settings2' => [
        'allowed' => [true, false],
        'default' => true
    ],
    
    'test_settings3' => [
        'allowed' => [true, false, 'true', 'false', 0, 1, '0', '1'],
        'default' => false
    ]
];
