<?php
declare(strict_types=1);

namespace Adminlocal\EcoRide\Helpers;

class ValidationHelper
{
    public static function validateModel(string $model): bool
    {
        $valid = ['Sedan', 'Coupe', 'SUV'];
        return in_array($model, $valid, true);
    }

    public static function validateColor(string $color): bool
    {
        if (preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
            return true;
        }
        $valid = ['Red', 'Blue', 'Green', 'Black', 'White'];
        return in_array($color, $valid, true);
    }

    public static function validateYear(string $year): bool
    {
        if (!preg_match('/^\d{4}$/', $year)) {
            return false;
        }
        $yearInt = (int) $year;
        $current = (int) date('Y');
        return $yearInt >= 1900 && $yearInt <= $current;
    }

    public static function validatePrice(string $price): bool
    {
        // Nombre positif, jusqu'à deux décimales
        if (!preg_match('/^\d+(?:\.\d{1,2})?$/', $price)) {
            return false;
        }
        return (float) $price >= 0;
    }

    public static function validateVIN(string $vin): bool
    {
        // 17 caractères alphanumériques (pas I, O, Q)
        return (bool) preg_match('/^[A-HJ-NPR-Z0-9]{17}$/', strtoupper($vin));
    }

    public static function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function validateMileage(string $mileage): bool
    {
        if (!preg_match('/^\d+$/', $mileage)) {
            return false;
        }
        return (int) $mileage >= 0;
    }

    public static function validateDate(string $date): bool
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d !== false && $d->format('Y-m-d') === $date;
    }
}
