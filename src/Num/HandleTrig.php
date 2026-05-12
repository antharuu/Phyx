<?php

declare(strict_types=1);

namespace Phyx\Num;

use Phyx\Enums\AngleUnit;

/**
 * Trigonometry helpers for {@see \Phyx\Num}.
 */
trait HandleTrig
{
    /**
     * Return the sine of an angle.
     *
     * Computes the sine. The input angle can be in radians or degrees as
     * specified by the {@see AngleUnit}.
     *
     * @param int|float $value The angle value.
     * @param AngleUnit $unit  The unit of the angle {@see AngleUnit}. Defaults to {@see AngleUnit::Radians}.
     *
     * @return float The sine of the angle.
     *
     * @example Num::sin(M_PI / 2)             // => 1.0
     * @example Num::sin(90, AngleUnit::Degrees) // => 1.0
     *
     * @see sin
     */
    public static function sin(int|float $value, AngleUnit $unit = AngleUnit::Radians): float
    {
        return sin($unit === AngleUnit::Degrees ? deg2rad($value) : $value);
    }

    /**
     * Return the cosine of an angle.
     *
     * Computes the cosine. The input angle can be in radians or degrees as
     * specified by the {@see AngleUnit}.
     *
     * @param int|float $value The angle value.
     * @param AngleUnit $unit  The unit of the angle {@see AngleUnit}. Defaults to {@see AngleUnit::Radians}.
     *
     * @return float The cosine of the angle.
     *
     * @example Num::cos(0)                 // => 1.0
     * @example Num::cos(180, AngleUnit::Degrees) // => -1.0
     *
     * @see cos
     */
    public static function cos(int|float $value, AngleUnit $unit = AngleUnit::Radians): float
    {
        return cos($unit === AngleUnit::Degrees ? deg2rad($value) : $value);
    }

    /**
     * Return the tangent of an angle.
     *
     * Computes the tangent. The input angle can be in radians or degrees as
     * specified by the {@see AngleUnit}.
     *
     * @param int|float $value The angle value.
     * @param AngleUnit $unit  The unit of the angle {@see AngleUnit}. Defaults to {@see AngleUnit::Radians}.
     *
     * @return float The tangent of the angle.
     *
     * @example Num::tan(0)                // => 0.0
     * @example Num::tan(45, AngleUnit::Degrees) // => 1.0
     *
     * @see tan
     */
    public static function tan(int|float $value, AngleUnit $unit = AngleUnit::Radians): float
    {
        return tan($unit === AngleUnit::Degrees ? deg2rad($value) : $value);
    }

    /**
     * Return the arcsine in the requested unit.
     *
     * Computes the principal value of the arcsine. Returns the result in radians
     * or degrees. Returns NAN if the value is outside [-1, 1].
     *
     * @param int|float $value The value whose arcsine is to be computed.
     * @param AngleUnit $unit  The desired unit {@see AngleUnit}. Defaults to {@see AngleUnit::Radians}.
     *
     * @return float The arcsine of the value.
     *
     * @example Num::asin(1)                 // => 1.5707963267948966
     * @example Num::asin(1, AngleUnit::Degrees) // => 90.0
     *
     * @see asin
     */
    public static function asin(int|float $value, AngleUnit $unit = AngleUnit::Radians): float
    {
        $result = asin($value);

        return $unit === AngleUnit::Degrees ? rad2deg($result) : $result;
    }

    /**
     * Return the arccosine in the requested unit.
     *
     * Computes the principal value of the arccosine. Returns the result in radians
     * or degrees. Returns NAN if the value is outside [-1, 1].
     *
     * @param int|float $value The value whose arccosine is to be computed.
     * @param AngleUnit $unit  The desired unit {@see AngleUnit}. Defaults to {@see AngleUnit::Radians}.
     *
     * @return float The arccosine of the value.
     *
     * @example Num::acos(1)                  // => 0.0
     * @example Num::acos(-1, AngleUnit::Degrees) // => 180.0
     *
     * @see acos
     */
    public static function acos(int|float $value, AngleUnit $unit = AngleUnit::Radians): float
    {
        $result = acos($value);

        return $unit === AngleUnit::Degrees ? rad2deg($result) : $result;
    }

    /**
     * Return the arctangent in the requested unit.
     *
     * Computes the principal value of the arctangent. Returns the result in
     * radians or degrees.
     *
     * @param int|float $value The value whose arctangent is to be computed.
     * @param AngleUnit $unit  The desired unit {@see AngleUnit}. Defaults to {@see AngleUnit::Radians}.
     *
     * @return float The arctangent of the value.
     *
     * @example Num::atan(0)                 // => 0.0
     * @example Num::atan(1, AngleUnit::Degrees) // => 45.0
     *
     * @see atan
     */
    public static function atan(int|float $value, AngleUnit $unit = AngleUnit::Radians): float
    {
        $result = atan($value);

        return $unit === AngleUnit::Degrees ? rad2deg($result) : $result;
    }

    /**
     * Convert degrees to radians.
     *
     * Multiplies the degree value by PI / 180.
     *
     * @param int|float $degrees The angle in degrees.
     *
     * @return float The angle in radians.
     *
     * @example Num::toRadians(180) // => 3.141592653589793
     * @example Num::toRadians(90)  // => 1.5707963267948966
     *
     * @see deg2rad
     */
    public static function toRadians(int|float $degrees): float
    {
        return deg2rad($degrees);
    }

    /**
     * Convert radians to degrees.
     *
     * Multiplies the radian value by 180 / PI.
     *
     * @param int|float $radians The angle in radians.
     *
     * @return float The angle in degrees.
     *
     * @example Num::toDegrees(M_PI)   // => 180.0
     * @example Num::toDegrees(M_PI_2) // => 90.0
     *
     * @see rad2deg
     */
    public static function toDegrees(int|float $radians): float
    {
        return rad2deg($radians);
    }
}
