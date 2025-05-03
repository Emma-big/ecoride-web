<?php
declare(strict_types=1);

namespace Tests\Helpers;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Adminlocal\EcoRide\Helpers\ValidationHelper;

class ValidationHelperTest extends TestCase
{
    #[DataProvider('modelProvider')]
    public function testValidateModel(string $model, bool $expected): void
    {
        $this->assertSame($expected, ValidationHelper::validateModel($model));
    }

    public static function modelProvider(): array
    {
        return [
            ['Sedan',          true],
            ['Coupe',          true],
            ['SUV',            true],
            ['',               false],
            ['InvalidModel',   false],
        ];
    }

    #[DataProvider('colorProvider')]
    public function testValidateColor(string $color, bool $expected): void
    {
        $this->assertSame($expected, ValidationHelper::validateColor($color));
    }

    public static function colorProvider(): array
    {
        return [
            ['Red',        true],
            ['Blue',       true],
            ['#FFFFFF',    true],
            ['#GGGGGG',    false],
            ['',           false],
            ['Invisible',  false],
        ];
    }

    #[DataProvider('yearProvider')]
    public function testValidateYear(string $year, bool $expected): void
    {
        $this->assertSame($expected, ValidationHelper::validateYear($year));
    }

    public static function yearProvider(): array
    {
        $current = (int) date('Y');
        return [
            ['2000',         true],
            ['1899',         false],
            [(string)$current, true],
            [(string)($current + 1), false],
            ['abcd',         false],
            ['',             false],
        ];
    }

    #[DataProvider('priceProvider')]
    public function testValidatePrice(string $price, bool $expected): void
    {
        $this->assertSame($expected, ValidationHelper::validatePrice($price));
    }

    public static function priceProvider(): array
    {
        return [
            ['100',     true],
            ['0',       true],
            ['99.99',   true],
            ['123.456', false],
            ['-5',      false],
            ['abc',     false],
            ['',        false],
        ];
    }

    #[DataProvider('vinProvider')]
    public function testValidateVIN(string $vin, bool $expected): void
    {
        $this->assertSame($expected, ValidationHelper::validateVIN($vin));
    }

    public static function vinProvider(): array
    {
        return [
            ['1HGCM82633A004352', true],
            ['1HGCM82633A00435I', false],
            ['123',               false],
            ['',                  false],
        ];
    }

    #[DataProvider('emailProvider')]
    public function testValidateEmail(string $email, bool $expected): void
    {
        $this->assertSame($expected, ValidationHelper::validateEmail($email));
    }

    public static function emailProvider(): array
    {
        return [
            ['user@example.com',                       true],
            ['user.name+tag+sorting@example.com',      true],
            ['invalid-email',                          false],
            ['',                                       false],
        ];
    }

    #[DataProvider('mileageProvider')]
    public function testValidateMileage(string $mileage, bool $expected): void
    {
        $this->assertSame($expected, ValidationHelper::validateMileage($mileage));
    }

    public static function mileageProvider(): array
    {
        return [
            ['0',       true],
            ['15000',   true],
            ['-1',      false],
            ['123.45',  false],
            ['abc',     false],
        ];
    }

    #[DataProvider('dateProvider')]
    public function testValidateDate(string $date, bool $expected): void
    {
        $this->assertSame($expected, ValidationHelper::validateDate($date));
    }

    public static function dateProvider(): array
    {
        return [
            ['2025-04-27', true],
            ['1999-12-31', true],
            ['2025-02-29', false],
            ['2025/04/27', false],
            ['',           false],
        ];
    }
}
