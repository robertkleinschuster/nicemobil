<?php

declare(strict_types=1);

namespace App\TeslaClient;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\RequestOptions;

final class TeslaClient
{
    private ?string $id = null;
    private ?string $access_token;
    private ?string $refresh_token;
    private ?string $id_token = null;
    private ?int $expires_in;
    private ?int $expires;
    private ?string $token_type;

    private string $state;
    private string $code;

    private ?string $proxy = null;

    private bool $redirected = false;

    private ?string $vehicleId = null;

    public function __construct(?array $data = [])
    {
        $this->id = uniqid();

        if (!isset($this->state)) {
            $this->state = $data['state'] ?? uniqid();
        }
        if (!isset($this->code)) {
            $this->code = $data['code'] ?? $this->generateCode();
        }
    }

    /**
     * @return mixed|string
     */
    public function getState(): mixed
    {
        return $this->state;
    }

    /**
     * @return mixed|string
     */
    public function getCode(): mixed
    {
        return $this->code;
    }

    /**
     * @param string|null $proxy
     */
    public function setProxy(?string $proxy): void
    {
        $this->proxy = $proxy;
    }

    public static function __set_state(array $data): object
    {
        $client = new TeslaClient();
        $client->id = $data['id'];
        $client->state = $data['state'];
        $client->code = $data['code'];
        $client->access_token = $data['access_token'];
        $client->refresh_token = $data['refresh_token'];
        $client->id_token = $data['id_token'];
        $client->expires_in = $data['expires_in'];
        $client->expires = $data['expires'];
        $client->token_type = $data['token_type'];
        $client->proxy = $data['proxy'] ?? null;
        $client->redirected = $data['redirected'] ?? false;
        $client->vehicleId = $data['vehicleId'] ?? null;

        return $client;
    }

    public function getProxy(): ?string
    {
        return $this->proxy;
    }

    /**
     * @throws TeslaClientException
     * @throws Exception
     */
    public function refreshAccessToken()
    {
        $requestBody = [
            "grant_type" => "refresh_token",
            "client_id" => "ownerapi",
            "refresh_token" => $this->refresh_token,
            "scope" => "openid email offline_access"
        ];

        $client = $this->getClient();
        $response = $client->post(
            'https://auth.tesla.com/oauth2/v3/token',
            [
                RequestOptions::HEADERS => ['content-type' => 'application/json'],
                RequestOptions::BODY => json_encode($requestBody),
                RequestOptions::PROXY => $this->getProxy(),
                'curl' => $this->getCurlOptions()
            ]
        );

        $responseData = json_decode($response->getBody()->getContents(), true);
        if (isset($responseData['access_token'])) {
            $this->access_token = $responseData['access_token'];
            $this->refresh_token = $responseData['refresh_token'];
            $this->id_token = $responseData['id_token'];
            $this->expires_in = $responseData['expires_in'];
            $this->token_type = $responseData['token_type'];
            $this->expires = time() + $this->expires_in;
            (new TeslaClientRepository())->save($this);
            return $responseData;
        }
        throw new TeslaClientException('Unable to refresh access_token');
    }

    private function getClient()
    {
        return new Client();
    }

    private function getCurlOptions(): array
    {
        if (defined('CURL_SSLVERSION_MAX_TLSv1_2')) {
            return [
                CURLOPT_SSLVERSION => CURL_SSLVERSION_MAX_TLSv1_2
            ];
        }
        return [];
    }

    public function fetchAccessToken(string $redirectUri)
    {
        $this->redirected = false;
        $url = new Uri($redirectUri);
        parse_str($url->getQuery(), $params);
        if (isset($params['code'])) {
            $requestBody = [
                "grant_type" => "authorization_code",
                "client_id" => "ownerapi",
                "code" => $params['code'],
                "code_verifier" => $this->code,
                "redirect_uri" => "https://auth.tesla.com/void/callback"
            ];
            $client = $this->getClient();
            $response = $client->post(
                'https://auth.tesla.com/oauth2/v3/token',
                [
                    RequestOptions::HEADERS => ['content-type' => 'application/json'],
                    RequestOptions::BODY => json_encode($requestBody),
                    RequestOptions::PROXY => $this->getProxy(),
                    'curl' => $this->getCurlOptions()
                ]
            );
            $responseData = json_decode($response->getBody()->getContents(), true);
            if (isset($responseData['access_token'])) {
                $this->access_token = $responseData['access_token'];
                $this->refresh_token = $responseData['refresh_token'];
                $this->id_token = $responseData['id_token'];
                $this->expires_in = $responseData['expires_in'];
                $this->token_type = $responseData['token_type'];
                $this->expires = time() + $this->expires_in;
                return $responseData;
            }
        }
        throw new TeslaClientException('Unable to fetch access_token');
    }

    public function getLoginUri()
    {
        $codeChallenge = rtrim(strtr(base64_encode(hash('sha256', $this->code, true)), '+/', '-_'), '=');
        $uri = new Uri('https://auth.tesla.com/oauth2/v3/authorize');
        $uri = $uri->withQuery(
            http_build_query([
                'client_id' => 'ownerapi',
                'code_challenge' => $codeChallenge,
                'code_challenge_method' => 'S256',
                'redirect_uri' => 'https://auth.tesla.com/void/callback',
                'response_type' => 'code',
                'scope' => 'openid email offline_access',
                'state' => $this->state,
            ])
        );
        return $uri;
    }


    private function generateCode(): string
    {
        $result = '';
        while (true) {
            $result .= uniqid();
            if (strlen($result) > 86) {
                return substr($result, 0, 86);
            }
        }
    }

    /**
     * @return ?string
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->access_token;
    }

    /**
     * @return string
     */
    public function getRefreshToken(): string
    {
        return $this->refresh_token;
    }

    /**
     * @return string
     */
    public function getIdToken(): ?string
    {
        return $this->id_token;
    }

    /**
     * @return int
     */
    public function getExpiresIn(): int
    {
        return $this->expires_in;
    }

    /**
     * @return int
     */
    public function getExpires(): int
    {
        return $this->expires;
    }

    /**
     * @return string
     */
    public function getTokenType(): string
    {
        return $this->token_type;
    }

    public function getEmail(): ?string
    {
        if (!$this->getIdToken()) {
            return null;
        }
        return json_decode(
            base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', $this->getIdToken())[1])))
        )->email;
    }

    public function getVehicles(): array
    {
        if (isset($this->expires)) {
            $this->expires_in = $this->expires - time();
            if ($this->expires_in < 3600) {
                $this->refreshAccessToken();
            }
        }

        $client = $this->getClient();
        $response = $client->get('https://owner-api.teslamotors.com/api/1/products', [
            RequestOptions::HEADERS => ['Authorization' => "Bearer {$this->getAccessToken()}"],
            RequestOptions::PROXY => $this->getProxy(),
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function getVehicle(string $id): array
    {
        if (isset($this->expires)) {
            $this->expires_in = $this->expires - time();
            if ($this->expires_in < 3600) {
                $this->refreshAccessToken();
            }
        }

        $client = $this->getClient();
        $response = $client->get("https://owner-api.teslamotors.com/api/1/vehicles/$id", [
            RequestOptions::HEADERS => ['Authorization' => "Bearer {$this->getAccessToken()}"]
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function getVehicleData(string|int $id): VehicleData
    {
        if (isset($this->expires)) {
            $this->expires_in = $this->expires - time();
            if ($this->expires_in < 3600) {
                $this->refreshAccessToken();
            }
        }

        $client = $this->getClient();

        $response = $client->get("https://owner-api.teslamotors.com/api/1/vehicles/$id/vehicle_data", [
            RequestOptions::HEADERS => ['Authorization' => "Bearer {$this->getAccessToken()}"],
            RequestOptions::PROXY => $this->getProxy(),
        ]);

        return new VehicleData(json_decode($response->getBody()->getContents(), true)['response'] ?? []);
    }

    public function setRedirected(bool $redirected): void
    {
        $this->redirected = $redirected;
    }

    public function isRedirected(): bool
    {
        return $this->redirected;
    }

    public function getVehicleId(): ?string
    {
        return $this->vehicleId;
    }

    public function setVehicleId(?string $vehicleId): void
    {
        $this->vehicleId = $vehicleId;
    }
}
