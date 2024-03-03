<?php

declare(strict_types=1);

namespace AppTest;

use App\Greeter;
use PHPUnit\Framework\TestCase;

class GreeterTest extends TestCase
{
    public function testGreet()
    {
        $greeter = new Greeter();
        $this->assertEquals('Good morning Isabell.', $greeter->greet('Isabell', 4));
        $this->assertEquals('Good night Isabell.', $greeter->greet('Isabell', 1));
        $this->assertEquals('Good afternoon Isabell.', $greeter->greet('Isabell', 15));
        $this->assertEquals('Good day Isabell.', $greeter->greet('Isabell', 11));
    }
}
