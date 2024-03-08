<?php

declare(strict_types=1);

use Mosaic\Fragment;

return function($children) {
    yield new Fragment('<main>');
    yield $children;
    yield new Fragment('</main>');
};