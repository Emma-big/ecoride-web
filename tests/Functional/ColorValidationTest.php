<?php

namespace Tests\Functional;

use PHPUnit\Framework\TestCase;
use Adminlocal\EcoRide\Helpers\ValidationHelper;

class ColorValidationTest extends TestCase
{
    public function testValidColorName()
    {
        $color = "Red"; // doit être Red, Blue, Green, Black, White
        $this->assertTrue(ValidationHelper::validateColor($color));
    }

    public function testValidColorHex()
    {
        $color = "#FF5733"; // code hexadécimal
        $this->assertTrue(ValidationHelper::validateColor($color));
    }

    public function testInvalidColor()
    {
        $color = "Rouge"; // pas dans la liste
        $this->assertFalse(ValidationHelper::validateColor($color));
    }
}
