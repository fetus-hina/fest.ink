<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components;

class Version
{
    public static function getVersion()
    {
        return \Yii::$app->version;
    }

    public static function getRevision()
    {
        return self::getGitLog('%H');
    }

    public static function getShortRevision()
    {
        return self::getGitLog('%h');
    }

    private static function getGitLog($format)
    {
        $cmdline = sprintf(
            '/usr/bin/env %s log -n 1 --format=%s',
            escapeshellarg('git'),
            escapeshellarg($format)
        );
        $lines = $status = null;
        $line = exec($cmdline, $lines, $status);
        if ($status !== 0) {
            return false;
        }
        return trim($line);
    }
}
