<?php

declare(strict_types=1);

use Mosaic\Renderer;

return fn(
    Renderer $renderer,
    string   $outsideTemp,
    string   $insideTemp,
    bool     $fastCharger,
    string   $fastChargerType,
    bool     $charging,
    string   $chargeRate
) => $renderer->fragment(<<<HTML
        <table>
            <tr>
                <td>

                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="feather feather-thermometer">
                        <path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"></path>
                    </svg>
                    Umgebung
                </td>
                <td>$outsideTemp</td>
            </tr>
            <tr>
                <td>

                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="feather feather-thermometer">
                        <path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"></path>
                    </svg>
                    Innenraum
                </td>
                <td>$insideTemp</td>
            </tr>

            {$renderer->conditional($renderer->fragment(<<<HTML
                <tr>
                    <td>

                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                             fill="none"
                             stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             class="feather feather-battery-charging">
                            <path
                                d="M5 18H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h3.19M15 6h2a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2h-3.19"></path>
                            <line x1="23" y1="13" x2="23" y2="11"></line>
                            <polyline points="11 6 7 12 13 12 9 18"></polyline>
                        </svg>
                        Schnelllader
                    </td>
                    <td>$fastChargerType</td>
                </tr>
HTML
            ), fn() => $fastCharger)}
            {$renderer->conditional($renderer->fragment(<<<HTML
                <tr>
                    <td>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                             fill="none"
                             stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             class="feather feather-battery-charging">
                            <path
                                d="M5 18H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h3.19M15 6h2a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2h-3.19"></path>
                            <line x1="23" y1="13" x2="23" y2="11"></line>
                            <polyline points="11 6 7 12 13 12 9 18"></polyline>
                        </svg>
                        Ladegeschwindigkeit
                    </td>
                    <td>$chargeRate</td>
                </tr>
HTML
            ), fn() => $charging)}
        </table>
HTML
);