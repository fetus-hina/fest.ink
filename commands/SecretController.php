<?php
namespace app\commands;

use yii\console\Controller;

class SecretController extends Controller
{
    public function actionCookie()
    {
        $strong = false;
        $binary = '';
        $length = 32;
        $binLength = ceil($length * 3 / 4);
        if (function_exists('openssl_random_pseudo_bytes')) {
            $binary = openssl_random_pseudo_bytes($binLength, $strong);
        }
        if ($binary === false || !$strong || strlen($binary) < $binLength) {
            $binary = file_get_contents('/dev/urandom', false, null, 0, $binLength);
        }
        if (strlen($binary) < $binLength) {
            throw new \Exception('Failed generating random key');
        }
        $key = substr(strtr(base64_encode($binary), '+/=', '_-.'), 0, $length);
        file_put_contents(
            __DIR__ . '/../config/cookie-secret.php',
            sprintf("<?php\nreturn '%s';\n", $key)
        );
    }
}
