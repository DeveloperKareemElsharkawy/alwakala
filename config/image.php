<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Image Driver
    |--------------------------------------------------------------------------
    |
    | Intervention Image supports "GD Library" and "Imagick" to process images
    | internally. You may choose one of them according to your PHP
    | configuration. By default PHP's "GD Library" implementation is used.
    |
    | Supported: "gd", "imagick"
    |
    */

    'driver' => 'gd',
    // TODO Change sizes like app
    'sizes' => [
        'medium' => [616, 904],
        'thumbnail' => [308, 452],
        'cover' => [500, 220]
    ],

    'allowed_file_types' => 'jpg,png,jpeg,gif',

    'max_file_size' => 6000000,
];
