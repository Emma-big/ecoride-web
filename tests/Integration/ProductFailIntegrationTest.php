<?php
declare(strict_types=1);

namespace Adminlocal\EcoRide\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Adminlocal\EcoRide\Helpers\ValidationHelper;

final class ProductFailIntegrationTest extends TestCase
{
    public function testProductValidationFails(): void
    {
        $model = "Trotinette X1"; // Invalide
        $color = "Violet";       // Invalide

        $isModelValid = ValidationHelper::validateModel($model);
        $isColorValid = ValidationHelper::validateColor($color);

        $saved = $isModelValid && $isColorValid;

        $this->assertFalse($saved);
    }
}
