<?php

namespace Tests\Functional;

use PHPUnit\Framework\TestCase;
use Adminlocal\EcoRide\Helpers\ValidationHelper;

class ModelValidationTest extends TestCase
{
    public function testValidModel()
    {
        $model = "Sedan"; // doit être Sedan, Coupe ou SUV
        $this->assertTrue(ValidationHelper::validateModel($model));
    }

    public function testInvalidModel()
    {
        $model = "Trotinette X1"; // modèle inconnu
        $this->assertFalse(ValidationHelper::validateModel($model));
    }
}
