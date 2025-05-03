<?php

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use Adminlocal\EcoRide\Helpers\ValidationHelper;

class ProductIntegrationTest extends TestCase
{
    public function testProductValidationAndSave()
    {
        $model = "Coupe"; // Valide
        $color = "Green"; // Valide

        $isModelValid = ValidationHelper::validateModel($model);
        $isColorValid = ValidationHelper::validateColor($color);

        $saved = $isModelValid && $isColorValid;

        $this->assertTrue($saved);
    }
}
