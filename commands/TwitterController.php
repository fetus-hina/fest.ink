<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\commands;

use Abraham\TwitterOAuth\TwitterOAuth;
use Yii;
use yii\console\Controller;
use app\components\helpers\Significant;
use app\models\Fest;

class TwitterController extends Controller
{
    public function actionUpdate()
    {
        $status = $this->makeTweet();
        if ($status === false) {
            return 2;
        }
        return $this->doTweet($status);
    }

    public function actionAuth()
    {
        // {{{
        $params = Yii::$app->params['twitter'];

        // リクエストトークンの発行
        $twitter = new TwitterOAuth($params['consumerKey'], $params['consumerSecret']);
        $ret = $twitter->oauth('oauth/request_token', ['callback' => 'oob']);
        if (!isset($ret['oauth_token']) || !isset($ret['oauth_token_secret'])) {
            return 1;
        }
        $oauthToken = $ret['oauth_token'];
        $oauthTokenSecret = $ret['oauth_token_secret'];
        $nextUrl = $twitter->url('oauth/authorize', ['oauth_token' => $oauthToken]);

        // ユーザに URL へアクセスしてもらう
        echo "次のURLへアクセスして認証してください:\n";
        echo "    {$nextUrl}\n";
    
        // PINコードを取得
        while (true) {
            echo "\n";
            echo "PINコードを入力:\n";
            echo "    ";
            $pin = trim(fgets(STDIN));
            if (preg_match('/^\d{7}$/', $pin)) {
                break;
            }
        }

        // ユーザトークンをもらう
        $twitter = new TwitterOAuth($params['consumerKey'], $params['consumerSecret'], $oauthToken, $oauthTokenSecret);
        $ret = $twitter->oauth('oauth/access_token', ['oauth_verifier' => $pin]);
        echo "===========================================\n";
        echo "Twitter ID: " . $ret['user_id'] . "\n";
        echo "ScreenName: " . $ret['screen_name'] . "\n";
        echo "-------------------------------------------\n";
        echo "    'userToken' => '" . $ret['oauth_token'] . "',\n";
        echo "    'userSecret' => '" . $ret['oauth_token_secret'] . "',\n";
        echo "-------------------------------------------\n";
        echo "config/twitter.php に token と secret をコピーしてください\n";
        // }}}
    }

    private function makeTweet()
    {
        // {{{
        $debug = false;

        $now = $debug ? strtotime('2015-09-13T10:00:00+09') : time();
        $fest = $this->getCurrentFest($now);
        if (!$fest) {
            echo "fest closed.\n";
            return false;
        }

        $query = (new \yii\db\Query())
            ->select([
                'SUM({{win_a}}.[[count]]) AS [[total_win_a]]',
                'SUM({{win_b}}.[[count]]) AS [[total_win_b]]',
                'MAX({{official_data}}.[[downloaded_at]]) AS [[last_updated_at]]',
            ])
            ->from('{{official_data}}')
            ->innerJoin(
                '{{official_win_data}} AS {{win_a}}',
                '{{official_data}}.[[id]] = {{win_a}}.[[data_id]]'
            )
            ->innerJoin(
                '{{official_win_data}} AS {{win_b}}',
                '{{official_data}}.[[id]] = {{win_b}}.[[data_id]]'
            )
            ->andWhere([
                '{{official_data}}.[[fest_id]]' => $fest->id,
                '{{win_a}}.[[color_id]]' => 1,
                '{{win_b}}.[[color_id]]' => 2,
            ]);
        if (!$sum = $query->createCommand()->queryOne()) {
            return false;
        }
        if ($sum['total_win_a'] == 0 || $sum['total_win_b'] == 0) {
            return false;
        }
        $alphaWinPercent = round($sum['total_win_a'] * 1000 / ($sum['total_win_a'] + $sum['total_win_b'])) / 10;
        $lastUpdated = new \DateTime('@' . $sum['last_updated_at']);
        $lastUpdated->setTimezone(new \DateTimeZone("Asia/Tokyo"));
        $signficantRange = Significant::significantRange($sum['total_win_a'], $sum['total_win_b']);
        $status = sprintf(
            "フェス\"%1\$s\"の推定勝率(%4\$s現在, N=%8\$d)\n%2\$s: %5\$.1f±%7\$.1f%%\n%3\$s: %6\$.1f±%7\$.1f%%\n",
            $fest->name,
            $fest->alphaTeam->name,
            $fest->bravoTeam->name,
            $lastUpdated->format('Y-m-d H:i T'),
            $alphaWinPercent,
            100 - $alphaWinPercent,
            ($signficantRange[1] - $signficantRange[0]) / 2,
            $sum['total_win_a'] + $sum['total_win_b']
        );
        $count = mb_strlen($status, 'UTF-8');
        if (140 - $count >= 23) {
            $status .= 'https://fest.ink/' . rawurlencode($fest->id);
            $count += 23;
        }
        $tags = [];
        foreach ($tags as $tag) {
            $tagLen = mb_strlen($tag, 'UTF-8') + 1; // +1 = space
            if (140 - $count >= $tagLen) {
                $status .= ' ' . $tag;
                $count += $tagLen;
            }
        }

        if ($debug) {
            echo $status . "\n";
            exit;
        }
        return $status;
        // }}}
    }

    private function doTweet($status)
    {
        // {{{
        $params = Yii::$app->params['twitter'];
        $twitter = new TwitterOAuth(
            $params['consumerKey'],
            $params['consumerSecret'],
            $params['userToken'],
            $params['userSecret']
        );

        for ($i = 0; $i < 3; ++$i) {
            if ($i > 0) {
                sleep(1);
            }
            $ret = $twitter->post('/statuses/update', ['status' => $status]);
            if (isset($ret->id_str)) {
                echo "ツイート完了:\n";
                printf(
                    "    https://twitter.com/%s/status/%s\n",
                    rawurlencode($ret->user->screen_name),
                    rawurlencode($ret->id_str)
                );
                return 0;
            }
        }
        echo "ツイート失敗\n";
        return 1;
        // }}}
    }

    private function getCurrentFest($now)
    {
        return Fest::find()
            ->andWhere(['<=', '{{fest}}.[[start_at]]', $now])
            ->andWhere(['>', '{{fest}}.[[end_at]]', $now])
            ->one();
    }
}
