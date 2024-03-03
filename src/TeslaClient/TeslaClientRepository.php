<?php

declare(strict_types=1);

namespace App\TeslaClient;

class TeslaClientRepository
{
    public function save(TeslaClient $client): void
    {
        $code = var_export($client, true);

        file_put_contents(
            'client.php',
            <<<PHP
<?php

return $code;
PHP
        );
    }

    public function load(): TeslaClient
    {
        $client = @include 'client.php';
        if (!$client) {
            return new TeslaClient();
        }
        return $client;
    }
}