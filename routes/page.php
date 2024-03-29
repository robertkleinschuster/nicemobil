<?php

declare(strict_types=1);

use App\Loading;
use App\TeslaClient\LiveModel;
use App\TeslaClient\TeslaClientRepository;
use Compass\Lazy;
use Mosaic\Renderer;
use Zenith\AppConfig;

return #[Lazy(loading: new Loading())] function (AppConfig $config, Renderer $renderer) {
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

        if ($model->isOnline()) {
            yield $renderer->args($data) => require  dirname(__DIR__) . "/components/icons.php";
            yield $renderer->fragment(<<<HTML
<nicemobil-live data-lat="{$model->getLatitude()}"
      data-lng="{$model->getLongitude()}">
   {$renderer->render(require  dirname(__DIR__) . "/components/battery.php", $renderer->args($data))}
   <div id="map"></div>
   {$renderer->render(require  dirname(__DIR__) . "/components/table.php", $renderer->args($data))}

</nicemobil-live>
HTML
);
        } else {
            yield require  dirname(__DIR__) . "/components/offline.php";
        }
    } else {
        yield require  dirname(__DIR__) . "/components/offline.php";
    }
};
