<?php

declare(strict_types=1);

use App\TeslaClient\LiveModel;
use App\TeslaClient\TeslaClientRepository;
use Mosaic\Fragment;

return function (\Zenith\AppConfig $config) {
    $repo = new TeslaClientRepository($config);
    $client = $repo->load();
    if ($client->getVehicleId() && $client->getIdToken()) {
        $vehicleData = $client->getVehicleData($client->getVehicleId());
        $model  = LiveModel::initFromVehicle($vehicleData);
        $data =  [
            'online' => $model->isOnline(),
            'odometer' => $model->getOdometer(),
            'speed' => $model->getSpeed(),
            'power' => $model->getPower(),
            'latitude' => $model->getLatitude(),
            'longitude' => $model->getLongitude(),
            'batteryState' => $model->getBatteryState(),
            'batteryRange' => $model->getBatteryRange(),
            'batteryLevel' => $model->getBatteryLevel(),
            'outsideTemp' =>  $model->getOutsideTemp(),
            'insideTemp' =>  $model->getInsideTemp(),
            'charging' => $model->isCharging(),
            'fastCharger' => $model->isFastCharger(),
            'fastChargerType' => $model->getFastChargerType(),
            'chargeRate' => $model->getChargeRate(),
        ];

        extract($data);
        ob_start();
        include "live.phtml";
        yield new Fragment(ob_get_clean());
    } else {
        yield 'Not logged in';
    }
};
