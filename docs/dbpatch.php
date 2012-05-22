<?php

return array(
    'limit' => 10,
    'default_branch' => 'default',
    'patch_directory' => '../patches',
    'patch_prefix' => 'patch',
    'color' => false,
    'dump_before_update' => false,
    'dump_directory' => '',
    # database settings
    'db' => array(
        'adapter' => 'Mysqli',
        'params' => array(
            'host' => 'localhost',
            'username' => 'user',
            'password' => 'pass',
            'dbname' => 'db',
            'charset' => 'utf8',
            'bin_dir' => '',
        )
    )
);

// Or, optionally retrieve an adapter object from the application:
//
// /* bootstrap your application here  */
// return array(
//     /* ... */
//     'db' => $bootstrap->getResource('db'),
// );
