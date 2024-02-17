<?php

use Doctum\Doctum;

return new Doctum('./src/', [
    'build_dir' => __DIR__ . '/docs/doctum',
    'cache_dir' => __DIR__ . '/docs/doctum/cache',
    'title' => 'API Documentation',
]);
