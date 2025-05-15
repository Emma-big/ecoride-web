<?php
declare(strict_types=1);

namespace Adminlocal\EcoRide\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Adminlocal\EcoRide\Helpers\ValidationHelper;

final class ProductIntegrationTest extends TestCase
{
    public function testProductValidationAndSave(): void
    {
        $model = "Coupe"; // Valide
        $color = "Green"; // Valide

        $isModelValid = ValidationHelper::validateModel($model);
        $isColorValid = ValidationHelper::validateColor($color);

        $saved = $isModelValid && $isColorValid;

        $this->assertTrue($saved);
    }
}
