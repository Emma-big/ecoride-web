<?php
declare(strict_types=1);

namespace Adminlocal\EcoRide\Tests\Functional;

use PHPUnit\Framework\TestCase;
use Adminlocal\EcoRide\Helpers\ValidationHelper;

final class ModelValidationTest extends TestCase
{
    public function testValidModel(): void
    {
        $model = "Sedan"; // doit être Sedan, Coupe ou SUV
        $this->assertTrue(ValidationHelper::validateModel($model));
    }

    public function testInvalidModel(): void
    {
        $model = "Trotinette X1"; // modèle inconnu
        $this->assertFalse(ValidationHelper::validateModel($model));
    }
}
