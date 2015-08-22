<?php
namespace app\commands;

use Yii;
use yii\console\Controller;
use Abraham\TwitterOAuth\TwitterOAuth;
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

        $now = $debug ? strtotime('2015-07-03 17:18:06+9') : time();
        $fest = $this->getCurrentFest($now);
        if (!$fest) {
            echo "fest closed.\n";
            false;
        }

        $query = (new \yii\db\Query())
            ->select([
                'SUM({{win_r}}.[[count]]) AS [[total_win_r]]',
                'SUM({{win_g}}.[[count]]) AS [[total_win_g]]',
                'MAX({{official_data}}.[[downloaded_at]]) AS [[last_updated_at]]',
            ])
            ->from('{{official_data}}')
            ->innerJoin(
                '{{official_win_data}} AS {{win_r}}',
                '{{official_data}}.[[id]] = {{win_r}}.[[data_id]]'
            )
            ->innerJoin(
                '{{official_win_data}} AS {{win_g}}',
                '{{official_data}}.[[id]] = {{win_g}}.[[data_id]]'
            )
            ->andWhere([
                '{{official_data}}.[[fest_id]]' => $fest->id,
                '{{win_r}}.[[color_id]]' => 1,
                '{{win_g}}.[[color_id]]' => 2,
            ]);
        if (!$sum = $query->createCommand()->queryOne()) {
            return false;
        }
        if ($sum['total_win_r'] == 0 || $sum['total_win_g'] == 0) {
            return false;
        }
        $redWinPercent = round($sum['total_win_r'] * 1000 / ($sum['total_win_r'] + $sum['total_win_g'])) / 10;
        $lastUpdated = new \DateTime('@' . $sum['last_updated_at']);
        $lastUpdated->setTimezone(new \DateTimeZone("Asia/Tokyo"));
        $status = sprintf(
            "フェス\"%s\"の推定勝率(%s現在)\n%sチーム: %.1f%%\n%sチーム: %.1f%%\n",
            $fest->name,
            $lastUpdated->format('Y-m-d H:i T'),
            $fest->redTeam->name,
            $redWinPercent,
            $fest->greenTeam->name,
            100 - $redWinPercent
        );
        $count = mb_strlen($status, 'UTF-8');
        if (140 - $count >= 23) {
            $status .= 'https://fest.ink/' . rawurlencode($fest->id);
            $count += 23;
        }
        $tags = [ '#Splatoon', '#スプラトゥーン', '#Splatfest' ];
        foreach ($tags as $tag) {
            $tagLen = mb_strlen($tag, 'UTF-8') + 1; // +1 = space
            if (140 - $count >= $tagLen) {
                $status .= ' ' . $tag;
                $count += $tagLen;
            }
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
