<?php

/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components;

use Exception;
use Yii;

class Version
{
    private static $revision = null;
    private static $shortRevision = null;

    public static function getVersion()
    {
        return Yii::$app->version;
    }

    public static function getRevision()
    {
        self::fetchRevision();
        return self::$revision;
    }

    public static function getShortRevision()
    {
        self::fetchRevision();
        return self::$shortRevision;
    }

    private static function fetchRevision()
    {
        if (self::$revision !== null && self::$shortRevision !== null) {
            return;
        }

        $data = self::fetchRevisionByFile() ?? self::fetchRevisionByGit();
        if (is_array($data) && count($data) === 2) {
            self::$revision = $data[0];
            self::$shortRevision = $data[1];
            return;
        }

        self::$revision = false;
        self::$shortRevision = false;
    }

    /**
     * @return array{string, string}|null
     */
    private static function fetchRevisionByFile(): array|null
    {
        $path = (string)Yii::getAlias('@app/REVISION');
        if (!file_exists($path)) {
            return null;
        }

        $line = trim((string)file_get_contents($path));
        if (preg_match('/^[0-9a-f]{40,}$/', $line)) {
            return [
                $line,
                substr($line, 0, 7),
            ];
        }

        return null;
    }

    /**
     * @return array{string, string}|null
     */
    private static function fetchRevisionByGit(): array|null
    {
        try {
            if (!$line = self::getGitLog('%H:%h')) {
                throw new Exception();
            }

            $revisions = explode(':', $line);
            if (count($revisions) !== 2) {
                throw new Exception();
            }

            return $revisions;
        } catch (Exception) {
            return null;
        }
    }

    private static function getGitLog($format)
    {
        $cmdline = sprintf(
            '/usr/bin/env %s log -n 1 --format=%s',
            escapeshellarg('git'),
            escapeshellarg($format)
        );
        $lines = $status = null;
        $line = @exec($cmdline, $lines, $status);
        if ($status !== 0) {
            return false;
        }
        return trim($line);
    }
}
