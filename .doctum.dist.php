<?php

use Doctum\Doctum;

return new Doctum('./src/', [
    'build_dir' => __DIR__ . '/docs/api',
    'cache_dir' => __DIR__ . '/docs/cache',
    'title' => 'API Documentation',
]);
