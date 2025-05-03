<?php

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use Adminlocal\EcoRide\Helpers\ValidationHelper;

class ProductFailIntegrationTest extends TestCase
{
    public function testProductValidationFails()
    {
        $model = "Trotinette X1"; // Invalide
        $color = "Violet"; // Invalide

        $isModelValid = ValidationHelper::validateModel($model);
        $isColorValid = ValidationHelper::validateColor($color);

        $saved = $isModelValid && $isColorValid;

        $this->assertFalse($saved);
    }
}
