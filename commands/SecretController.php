<?php
namespace app\commands;

use yii\console\Controller;

class SecretController extends Controller
{
    public function actionCookie()
    {
        $length = 32;
        $binLength = (int)ceil($length * 3 / 4);
        $binary = random_bytes($binLength); // PHP 7 native random_bytes() or compat-lib's one
        $key = substr(strtr(base64_encode($binary), '+/=', '_-.'), 0, $length);
        file_put_contents(
            __DIR__ . '/../config/cookie-secret.php',
            sprintf("<?php\nreturn '%s';\n", $key)
        );
    }
}
