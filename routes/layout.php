<?php

declare(strict_types=1);

use Zenith\Components\Dependencies;
use Zenith\Components\Document;
use Mosaic\Renderer;

return fn($children, Renderer $renderer, array $params, array $queryParams) => new Document(
    lang: 'de',
    children: [
        $renderer->fragment(<<<HTML
<header>
    <nav>
        <a href="https://nicemobil.blog/">Aktuelles</a>
        <a href="https://nicemobil.blog/ueber/">Über</a>
        <a href="https://nicemobil.blog/kontakt/">Kontakt</a>
    </nav>

    <h1>NICEmobil</h1>
</header>
HTML
),
        $children,
        $renderer->fragment(<<<HTML
<footer>
    <a href="https://nicemobil.blog/impressum/">Impressum</a>
</footer>
HTML
        )
    ],
    dependencies: new Dependencies(
        scripts: ['/leaflet/leaflet.js', '/live.js'],
        stylesheets: ['/simple.min.css', '/leaflet/leaflet.css', '/styles.css']
    ),
    title: 'Live - NICEmobil',
    description: 'Das NICEmobil von Franz Liebmann online verfolgen.',
);
