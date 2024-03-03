<?php

namespace App\TeslaClient;

final class VehicleData
{
    private const MILES_TO_KM = 1.60934;
    private const BATTERY_LEVEL_MEDIUM = 40;
    private const BATTERY_LEVEL_LOW = 20;

    private int $timestamp = 0;

    private bool $online = false;
    private int $odometer = 0;
    private float $latitude = 0;
    private float $longitude = 0;
    private int $speed = 0;
    private int $power = 0;
    private float $outsideTemp = 0;
    private float $insideTemp = 0;
    private bool $fastChargerPresent = false;
    private string $fastChargerType = '';
    private string $chargingState = '';
    private int $chargeRate = 0;
    private int $usableBatteryLevel = 0;
    private int $idealBatteryRange = 0;

    final public function __construct(array $apiData)
    {
        if (isset($apiData['vehicle_state']['odometer'])) {
            $this->online = true;
            $this->odometer = (int)$apiData['vehicle_state']['odometer'];
        }
        if (isset($apiData['drive_state']['latitude'])) {
            $this->latitude = (float)$apiData['drive_state']['latitude'];
        }
        if (isset($apiData['drive_state']['longitude'])) {
            $this->longitude = (float)$apiData['drive_state']['longitude'];
        }
        if (isset($apiData['drive_state']['speed'])) {
            $this->speed = (int)$apiData['drive_state']['speed'];
        }
        if (isset($apiData['drive_state']['power'])) {
            $this->power = (int)$apiData['drive_state']['power'];
        }
        if (isset($apiData['climate_state']['outside_temp'])) {
            $this->outsideTemp = (float)$apiData['climate_state']['outside_temp'];
        }
        if (isset($apiData['climate_state']['inside_temp'])) {
            $this->insideTemp = (float)$apiData['climate_state']['inside_temp'];
        }
        if (isset($apiData['charge_state']['fast_charger_present'])) {
            $this->fastChargerPresent = (bool)$apiData['charge_state']['fast_charger_present'];
        }
        if (isset($apiData['charge_state']['fast_charger_type'])) {
            $this->fastChargerType = (string)$apiData['charge_state']['fast_charger_type'];
        }
        if (isset($apiData['charge_state']['charging_state'])) {
            $this->chargingState = (string)$apiData['charge_state']['charging_state'];
        }
        if (isset($apiData['charge_state']['charge_rate'])) {
            $this->chargeRate = (int)$apiData['charge_state']['charge_rate'];
        }
        if (isset($apiData['charge_state']['usable_battery_level'])) {
            $this->usableBatteryLevel = (int)$apiData['charge_state']['usable_battery_level'];
        }
        if (isset($apiData['charge_state']['ideal_battery_range'])) {
            $this->idealBatteryRange = (int)$apiData['charge_state']['ideal_battery_range'];
        }
    }

    private function convertMilesToKM(int $miles): int
    {
        return (int)round($miles * self::MILES_TO_KM);
    }

    /**
     * @return bool
     */
    public function isOnline(): bool
    {
        return $this->online;
    }

    /**
     * @return int
     */
    public function getOdometer(): int
    {
        return $this->odometer;
    }

    /**
     * @return int
     */
    public function getOdometerKM(): int
    {
        return $this->convertMilesToKM($this->getOdometer());
    }

    /**
     * @return float|int
     */
    public function getLatitude(): float|int
    {
        return $this->latitude;
    }

    /**
     * @return float|int
     */
    public function getLongitude(): float|int
    {
        return $this->longitude;
    }

    /**
     * @return int
     */
    public function getSpeed(): int
    {
        return $this->speed;
    }

    /**
     * @return int
     */
    public function getSpeedKMh(): int
    {
        return $this->convertMilesToKM($this->getSpeed());
    }

    /**
     * @return int
     */
    public function getPower(): int
    {
        return $this->power;
    }

    /**
     * @return float|int
     */
    public function getOutsideTemp(): float|int
    {
        return $this->outsideTemp;
    }

    /**
     * @return float|int
     */
    public function getInsideTemp(): float|int
    {
        return $this->insideTemp;
    }

    /**
     * @return bool
     */
    public function isFastChargerPresent(): bool
    {
        return $this->fastChargerPresent;
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
    public function getChargingState(): string
    {
        return $this->chargingState;
    }

    public function isCharging(): bool
    {
        return $this->getChargingState() === 'Charging';
    }

    /**
     * @return int
     */
    public function getChargeRate(): int
    {
        return $this->chargeRate;
    }

    /**
     * @return int
     */
    public function getChargeRateKMh(): int
    {
        return $this->convertMilesToKM($this->getChargeRate());
    }

    /**
     * @return int
     */
    public function getUsableBatteryLevel(): int
    {
        return $this->usableBatteryLevel;
    }

    /**
     * @return int
     */
    public function getIdealBatteryRange(): int
    {
        return $this->idealBatteryRange;
    }

    public function getIdealBatteryRangeKM(): int
    {
        return $this->convertMilesToKM($this->idealBatteryRange);
    }

    public function getChargeLevelCategory(): string
    {
        if ($this->isCharging()) {
            return 'charging';
        }

        if ($this->getUsableBatteryLevel() < self::BATTERY_LEVEL_LOW) {
            return 'low';
        }

        if ($this->getUsableBatteryLevel() < self::BATTERY_LEVEL_MEDIUM) {
            return 'medium';
        }

        return 'high';
    }

    public function getMapTileX(): int
    {
        return (int)floor((($this->getLongitude() + 180) / 360) * pow(2, $this->getMapZoom()));
    }

    public function getMapZoom(): int
    {
        return 15;
    }

    public function getMapTileY(): int
    {
        return (int)floor(
            (1 - log(tan(deg2rad($this->getLatitude())) + 1 / cos(deg2rad($this->getLatitude()))) / pi()) / 2 * pow(
                2,
                $this->getMapZoom()
            )
        );
    }

    public function getMapTile(): string
    {
        return "/{$this->getMapZoom()}/{$this->getMapTileX()}/{$this->getMapTileY()}.png";
    }

    public function isExpired(): bool
    {
        return time() - $this->timestamp > 30;
    }

    public function toStorage(): array
    {
        return [
            'timestamp' => time(),
            'latitude' => $this->getLatitude(),
            'longitude' => $this->getLongitude(),
            'odometer' => $this->getOdometer(),
            'speed' => $this->getSpeed(),
            'chargeRate' => $this->getChargeRate(),
            'chargingState' => $this->getChargingState(),
            'fastChargerType' => $this->getFastChargerType(),
            'idealBatteryRange' => $this->getIdealBatteryRange(),
            'usableBatteryLevel' => $this->getUsableBatteryLevel(),
            'insideTemp' => $this->getInsideTemp(),
            'outsideTemp' => $this->getOutsideTemp(),
            'power' => $this->getPower(),
            'charging' => $this->isCharging(),
            'online' => $this->isOnline(),
            'fastChargerPresent' => $this->isFastChargerPresent(),
        ];
    }

    public static function fromStorage(array $data): static
    {
        $vehicle = new VehicleData([]);
        $vehicle->timestamp = $data['timestamp'];
        $vehicle->latitude = $data['latitude'];
        $vehicle->longitude = $data['longitude'];
        $vehicle->chargeRate = $data['chargeRate'];
        $vehicle->chargingState = $data['chargingState'];
        $vehicle->fastChargerType = $data['fastChargerType'];
        $vehicle->idealBatteryRange = $data['idealBatteryRange'];
        $vehicle->usableBatteryLevel = $data['usableBatteryLevel'];
        $vehicle->insideTemp = $data['insideTemp'];
        $vehicle->outsideTemp = $data['outsideTemp'];
        $vehicle->power = $data['power'];
        $vehicle->online = $data['online'];
        $vehicle->fastChargerPresent = $data['fastChargerPresent'];
        $vehicle->odometer = $data['odometer'];
        return $vehicle;
    }

    public static function __set_state(array $data): object
    {
        return self::fromStorage($data);
    }
}
