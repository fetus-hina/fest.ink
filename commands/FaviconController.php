<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use Zend\Crypt\FileCipher;

class FaviconController extends Controller
{
    public function actionEncrypt()
    {
        $licenseKey = $this->readLicenseKey();
        $engine = $this->createCryptEngine();
        $engine->setKey($licenseKey);
        $status = $engine->encrypt(
            Yii::getAlias('@app/data/favicon/ikagirl.png'),
            Yii::getAlias('@app/data/favicon/ikagirl.dat')
        );
        if (!$status) {
            $this->stdout("Failed to create ikagirl.dat\n", Console::FG_RED);
            @unlink(Yii::getAlias('@app/data/ikagirl.dat'));
            return 1;
        }
        $this->stdout("Created ikagirl.dat\n", Console::FG_GREEN);
    }

    public function actionDecrypt()
    {
        $licenseKey = $this->readLicenseKey(true);
        if ($licenseKey === null) {
            touch(Yii::getAlias('@app/data/favicon/ikagirl.png'));
            $this->stdout("SKIPPED\n", Console::FG_YELLOW);
            return;
        }
        @unlink(Yii::getAlias('@app/data/favicon/ikagirl.png'));
        $engine = $this->createCryptEngine();
        $engine->setKey($licenseKey);
        $status = $engine->decrypt(
            Yii::getAlias('@app/data/favicon/ikagirl.dat'),
            Yii::getAlias('@app/data/favicon/ikagirl.png')
        );
        if (!$status) {
            $this->stdout("Failed to create ikagirl.png\n", Console::FG_RED);
            @unlink(Yii::getAlias('@app/data/favicon/ikagirl.png'));
            return 1;
        }
        $this->stdout("Created ikagirl.png\n", Console::FG_GREEN);
    }

    private function readLicenseKey($allowEmpty = false)
    {
        while (true) {
            $this->stdout("Please input favicon license key");
            if ($allowEmpty) {
                $this->stdout("(empty if unlicensed)", Console::FG_YELLOW);
            }
            $this->stdout(":\n    ");
            $line = trim(fgets(STDIN));
            if (preg_match('/^[!-~]{32}$/', $line)) {
                return $line;
            } elseif ($line == '' && $allowEmpty) {
                return null;
            }
        }
    }

    private function createCryptEngine()
    {
        $engine = new FileCipher();
        $engine->setKeyIteration(65535);
        $engine->setHashAlgorithm('sha256');
        $engine->setPbkdf2HashAlgorithm('sha256');
        return $engine;
    }

}
