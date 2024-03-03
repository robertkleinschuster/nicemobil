<?php

declare(strict_types=1);

namespace App;

class Greeter
{
    public function greet(string $name, int $hour): string
    {
        if ($hour > 22 || $hour < 4) {
            return "Good night $name.";
        }
        if ($hour > 14) {
            return "Good afternoon $name.";
        }
        if ($hour > 10) {
            return "Good day $name.";
        }

        return "Good morning $name.";
    }
}