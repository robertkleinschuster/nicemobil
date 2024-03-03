<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface;

return function (array $parsedBody, ResponseInterface $response) {
    if (!isset($parsedBody['name'])) {
        return $response->withStatus(400);
    }
    return $response->withStatus(302)->withHeader('Location', '/greetings/' . $parsedBody['name']);
};