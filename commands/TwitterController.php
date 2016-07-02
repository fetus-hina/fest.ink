<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\commands;

use Abraham\TwitterOAuth\TwitterOAuth;
use Exception;
use Yii;
use app\components\helpers\Significant;
use app\models\Fest;
use yii\console\Controller;

class TwitterController extends Controller
{
    /**
     * @var string 検索基準となる日時を strtotime() が解釈できる形式で指定します。
     */
    public $time = null;

    public function init()
    {
        parent::init();
        Yii::$app->timeZone = 'Asia/Tokyo';
    }

    public function options(/*string*/ $action) /* : array */
    {
        switch ($action) {
            case 'update':
                return ['time'];

            default:
                return [];
        }
    }

    public function optionAliases() /* : array */
    {
        return [
            't' => 'time',
        ];
    }

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

        $fest = $this->getCurrentFest();
        if (!$fest) {
            echo "fest closed.\n";
            return false;
        }

        $time = $this->getTime();
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
            ->andWhere(['and',
                [
                    '{{official_data}}.[[fest_id]]' => $fest->id,
                    '{{win_a}}.[[color_id]]' => 1,
                    '{{win_b}}.[[color_id]]' => 2,
                ],
                ['<=', '{{official_data}}.[[downloaded_at]]', $time],
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
        list($teamNameA, $teamNameB) = (function ($a, $b) {
            $len = mb_strlen($a, 'UTF-8');
            for ($i = 0; $i < $len; ++$i) {
                $tmpA = mb_substr($a, $i, 1, 'UTF-8');
                $tmpB = mb_substr($b, $i, 1, 'UTF-8');
                if ($tmpA !== $tmpB) {
                    return [$tmpA, $tmpB];
                }
            }
            return ['Ａ', 'Ｂ'];
        })($fest->alphaTeam->name, $fest->bravoTeam->name);
        $lines = [];
        $lines[] = sprintf('フェス「%s」の推定勝率(%s現在; N=%s)',
            $fest->name,
            $lastUpdated->format('Y-m-d H:i'),
            number_format($sum['total_win_a'] + $sum['total_win_b'])
        );
        $lines[] = (function () use ($sum, $fest, $teamNameA, $teamNameB) {
            switch (Significant::isSignificant($sum['total_win_a'], $sum['total_win_b'])) {
                case Significant::P_0_01:
                    return sprintf(
                        '【%sチーム優勢の模様】',
                        $sum['total_win_a'] > $sum['total_win_b']
                            ? $teamNameA
                            : $teamNameB
                    );

                case Significant::P_0_05:
                    return sprintf(
                        '【%sチーム優勢?】',
                        $sum['total_win_a'] > $sum['total_win_b']
                            ? $teamNameA
                            : $teamNameB
                    );

                default:
                    return '【優劣不明】';
            }
        })();
        $lines[] = sprintf('%s: %.1f～%.1f%%',
            $teamNameA,
            $signficantRange[0],
            $signficantRange[1]
        );
        $lines[] = sprintf('%s: %.1f～%.1f%%',
            $teamNameB,
            100 - $signficantRange[1],
            100 - $signficantRange[0]
        );
        $status = implode("\n", $lines);
        $appends = [
            "\n" . 'https://fest.ink/' . rawurlencode($fest->id),
        ];
        $twValidation = \Twitter_Validation::create('', ['short_url_length' => 23, 'short_url_length_https' => 23]);
        foreach ($appends as $appendText) {
            if ($twValidation->getTweetLength($status . $appendText) <= 140) {
                $status .= $appendText;
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

    private function getCurrentFest() /* : ?Fest */
    {
        $time = $this->getTime();
        fprintf(STDERR, "%s: %s に開催中のフェスを取得しています...\n", __METHOD__, date('Y-m-d\TH:i:sO', (int)$time));
        $ret = Fest::find()
            ->andWhere(['<=', '{{fest}}.[[start_at]]', $time])
            ->andWhere(['>', '{{fest}}.[[end_at]]', $time])
            ->one();
        fwrite(STDERR, $ret
            ? sprintf("%s: 開催中フェス: #%d, %s\n", __METHOD__, $ret->id, $ret->name)
            : (__METHOD__ . ": フェスは見つかりませんでした\n")
        );
        return $ret;
    }

    public function getTime() : int
    {
        $now = $_SERVER['REQUEST_TIME'] ?? time();
        if ($this->time === null || trim((string)$this->time) === '') {
            return $now;
        }
        if (is_scalar($this->time)) {
            $t = @strtotime($this->time, $now);
        } else {
            $t = false;
        }
        if ($t === false) {
            throw new Exception('Invalid time value: "' . (string)$this->time . '"');
        }
        return (int)$t;
    }
}
