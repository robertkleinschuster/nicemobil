<?php

declare(strict_types=1);

if (PHP_SAPI == 'cli-server') {
    $url = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . '/public' . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

require "public/index.php";