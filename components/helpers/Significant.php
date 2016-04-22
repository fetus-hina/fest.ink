<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components\helpers;

class Significant
{
    const P_0_01 = 'p<.01';
    const P_0_05 = 'p<.05';
    const NS = 'n.s.';

    public static function isSignificant($alpha, $bravo)
    {
        $expected = ($alpha + $bravo) / 2;
        if ($expected == 0 || is_nan($expected)) {
            return false;
        }
            
        $a = pow($alpha - $expected, 2) / $expected;
        $b = pow($bravo - $expected, 2) / $expected;
        $chi2 = $a + $b;
        if ($chi2 >= 6.63490) {
            return static::P_0_01;
        } else if ($chi2 >= 3.84146) {
            return static::P_0_05;
        } else {
            return static::NS;
        }
    }

    public static function significantRange($alpha, $bravo)
    {
        $total = $alpha + $bravo;
        if ($total == 0 || is_nan($total)) {
            return false;
        }
        $expected = $total / 2;
        $lower = null;
        $upper = null;
        for ($permil = 1; $permil < 1000; ++$permil) {
            $assumeAlpha = round($total * $permil / 1000);
            $assumeBravo = $total - $assumeAlpha;
            $sA = $alpha + $assumeAlpha;
            $sB = $bravo + $assumeBravo;
            $chi2 = (2 * $total) * pow($alpha * $assumeBravo - $bravo * $assumeAlpha, 2) / ($sA * $sB * $total * $total);
            if ($chi2 < 3.84146) {
                if ($lower === null) {
                    $lower = $permil;
                }
                $upper = $permil;
            } else if ($upper !== null) {
                break;
            }
        }
        return [ $lower / 10, $upper / 10 ];
    }
}