<?php

declare(strict_types=1);

use App\TeslaClient\TeslaClientRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

return function (ServerRequestInterface $request, ResponseInterface $response, array $parsedBody) {
    $repo = new TeslaClientRepository();
    $client = $repo->load();
    if (isset($parsedBody['vehicle'])) {
        $client->setVehicleId($parsedBody['vehicle']);
        $repo->save($client);
        return $response->withStatus(302)->withHeader('Location', (string)$request->getUri());
    } else if (isset($parsedBody['url'])) {
        $client->fetchAccessToken($parsedBody['url']);
        $repo->save($client);
        return $response->withStatus(302)->withHeader('Location', (string)$request->getUri());
    } else {
        $client->setRedirected(true);
        $repo->save($client);
        return $response->withStatus(302)->withHeader('Location', (string)$client->getLoginUri());
    }
};