<?php

declare(strict_types=1);

namespace App\TeslaClient;


class LiveModel
{
    private bool $online;
    private string $odometer;
    private string $speed;
    private string $power;
    private float $latitude;
    private float $longitude;
    private string $batteryState;
    private string $batteryRange;
    private int $batteryLevel;
    private string $outsideTemp;
    private string $insideTemp;
    private bool $charging;
    private bool $fastCharger;
    private string $fastChargerType;
    private string $chargeRate;

    public static function initFromVehicle(VehicleData $vehicleData): LiveModel
    {
        $model = new LiveModel([]);
        $model->online = $vehicleData->isOnline();
        $model->odometer = $model->formatNumber($vehicleData->getOdometerKM(), 'km');
        $model->speed = $model->formatNumber($vehicleData->getSpeedKMh(), 'km/h');
        $model->power = $model->formatNumber($vehicleData->getPower(), 'kW');
        $model->latitude = (float)$vehicleData->getLatitude();
        $model->longitude = (float)$vehicleData->getLongitude();
        $model->batteryState = $vehicleData->getChargeLevelCategory();
        $model->batteryRange = $model->formatNumber($vehicleData->getIdealBatteryRangeKM(), 'km');
        $model->batteryLevel = $vehicleData->getUsableBatteryLevel();
        $model->outsideTemp = $model->formatNumber($vehicleData->getOutsideTemp(), '°C', 1);
        $model->insideTemp = $model->formatNumber($vehicleData->getInsideTemp(), '°C', 1);
        $model->charging = $vehicleData->isCharging();
        $model->fastCharger = $vehicleData->isFastChargerPresent();
        $model->fastChargerType = $vehicleData->getFastChargerType();
        $model->chargeRate = $model->formatNumber($vehicleData->getChargeRateKMh(), 'km/h');
        return $model;
    }

    private function formatNumber(float|int $number, string $unit, int $decimals = 0): string
    {
        return number_format($number, $decimals, ',', ' ') . ' ' . $unit;
    }

    /**
     * @return bool
     */
    public function isOnline(): bool
    {
        return $this->online;
    }

    /**
     * @return string
     */
    public function getOdometer(): string
    {
        return $this->odometer;
    }

    /**
     * @return string
     */
    public function getSpeed(): string
    {
        return $this->speed;
    }

    /**
     * @return string
     */
    public function getPower(): string
    {
        return $this->power;
    }

    /**
     * @return float
     */
    public function getLatitude(): float
    {
        return $this->latitude;
    }

    /**
     * @return float
     */
    public function getLongitude(): float
    {
        return $this->longitude;
    }

    /**
     * @return string
     */
    public function getBatteryState(): string
    {
        return $this->batteryState;
    }

    /**
     * @return string
     */
    public function getBatteryRange(): string
    {
        return $this->batteryRange;
    }

    /**
     * @return int
     */
    public function getBatteryLevel(): int
    {
        return $this->batteryLevel;
    }

    /**
     * @return string
     */
    public function getOutsideTemp(): string
    {
        return $this->outsideTemp;
    }

    /**
     * @return string
     */
    public function getInsideTemp(): string
    {
        return $this->insideTemp;
    }

    /**
     * @return bool
     */
    public function isCharging(): bool
    {
        return $this->charging;
    }

    /**
     * @return bool
     */
    public function isFastCharger(): bool
    {
        return $this->fastCharger;
    }

    /**
     * @return string
     */
    public function getFastChargerType(): string
    {
        return $this->fastChargerType;
    }

    /**
     * @return string
     */
    public function getChargeRate(): string
    {
        return $this->chargeRate;
    }
}