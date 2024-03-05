<?php

declare(strict_types=1);

namespace App\TeslaClient;

use Zenith\AppConfig;

class TeslaClientRepository
{
    private ?string $dataDir;

    public function __construct(AppConfig $config)
    {
        $this->dataDir = $config->get('data_dir');
    }

    public function save(TeslaClient $client): void
    {
        $code = var_export($client, true);

        file_put_contents(
            $this->dataDir . DIRECTORY_SEPARATOR . 'client.php',
            <<<PHP
<?php

return $code;
PHP
        );
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
    }

    public function load(): TeslaClient
    {
        $client = @include $this->dataDir . DIRECTORY_SEPARATOR . 'client.php';
        if (!$client) {
            return new TeslaClient();
        }
        return $client;
    }
}