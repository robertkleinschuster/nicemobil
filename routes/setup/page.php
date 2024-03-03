<?php

declare(strict_types=1);

use App\TeslaClient\TeslaClientRepository;
use Robs\Component\App\Components\Button;
use Robs\Component\App\Components\Form;
use Robs\Component\App\Components\InputGroup;
use Robs\Component\App\Plugin\Url;
use Robs\Component\Renderer\Fragment;

return function () {
    $repo = new TeslaClientRepository();
    $client = $repo->load();

    if ($client->isRedirected()) {
        yield new Form(
            action: new Url('/setup'),
            children: [
                new InputGroup('text', 'url', 'URL: '),
                new Button('submit', 'Login')
            ]
        );
    } else if ($client->getIdToken()) {
        yield $client->getEmail();
        if ($client->getVehicleId()) {
            $vehicle = $client->getVehicle($client->getVehicleId());
            if (isset($vehicle['response'])) {
                yield ' (' . $vehicle['response']['display_name'] . ')';
            }
        } else {
            $vehicles = $client->getVehicles();
            if (isset($vehicles['response']) && is_array($vehicles['response'])) {
                yield new Form(
                    action: new Url('/setup'),
                    children: function () use ($vehicles) {
                        yield new Fragment('<select name="vehicle">');
                        foreach ($vehicles['response'] as $vehicle) {
                            yield new Fragment(sprintf('<option value="%s">%s</option>', $vehicle['id'], $vehicle['display_name']));
                        }
                        yield new Fragment('</select>');
                        yield new Button('submit', 'Select');
                    }
                );
            }
        }
    } else {
        yield new Form(
            action: new Url('/setup'),
            children: [
                new Button('submit', 'Login'),
            ],
            target: '_blank'
        );
    }
};