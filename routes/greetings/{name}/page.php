<?php

declare(strict_types=1);

use App\Greeter;

return function (array $params) {
    $greeter = new Greeter();
    return $greeter->greet($params['name'], (int)date('H'));
};