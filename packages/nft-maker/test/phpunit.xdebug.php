<?php declare(strict_types=1);
if (!\function_exists('xdebug_set_filter')) {
    return;
}

\xdebug_set_filter(256, 1, [realpath(__DIR__ . '/../src')]);
