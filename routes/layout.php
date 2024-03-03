<?php

declare(strict_types=1);

use Robs\Component\App\Components\Dependencies;
use Robs\Component\App\Components\Document;
use Robs\Component\Renderer\Renderer;

return fn($children, Renderer $renderer, array $params, array $queryParams) => new Document(
    lang: 'de',
    children: $children,
    dependencies: new Dependencies(
        scripts: ['/leaflet/leaflet.js', '/live.js'],
        stylesheets: ['/simple.min.css', '/leaflet/leaflet.css', '/styles.css']
    ),
    title: 'Live - NICEmobil',
    description: 'Das NICEmobil von Franz Liebmann online verfolgen.',
);
