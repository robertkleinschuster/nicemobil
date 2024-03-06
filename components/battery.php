<?php

declare(strict_types=1);

use Mosaic\Renderer;

return fn(Renderer $renderer, string $batteryState, int $batteryLevel, string $batteryRange) => $renderer->fragment(<<<HTML
      <p>
            <span class="status">Live</span>
            <span class="battery">
                <span class="battery__level $batteryState"
                      style="width: calc($batteryLevel% - 2px);">
                    <span class="battery__label">$batteryRange</span>
                </span>
            </span>
      </p>
HTML
);