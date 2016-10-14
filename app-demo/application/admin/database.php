<?php


return config_select([
'default' => [
    'driver'    => 'mysql',
    'host'      => '10.1.14.16',
    'database'  => 'bbframework',
    'username'  => 'root',
    'password'  => '461he.com',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => 'bb_',
],
'test' => [
    'driver'    => 'mysql',
    'host'      => '127.0.0.1',
    'database'  => 'bbframework',
    'username'  => 'root',
    'password'  => '461he.com',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => 'bb_',
]
]);


// return [
//     'driver'    => 'mysql',
//     'host'      => '127.0.0.1',
//     'database'  => 'bbframework',
//     'username'  => 'root',
//     'password'  => 'sinyee123',
//     'charset'   => 'utf8',
//     'collation' => 'utf8_unicode_ci',
//     'prefix'    => 'bb_',
// ];


