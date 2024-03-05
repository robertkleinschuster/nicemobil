<?php

declare(strict_types=1);

use Zenith\Components\Dependencies;
use Zenith\Components\Document;
use Mosaic\Renderer;

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
