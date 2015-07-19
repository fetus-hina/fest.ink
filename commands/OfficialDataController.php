<?php
namespace app\commands;

use Curl\Curl;
use Yii;
use app\components\json\OfficialJson;
use app\models\Fest;
use app\models\OfficialData;
use app\models\OfficialWinData;
use yii\console\Controller;

class OfficialDataController extends Controller
{
    public function actionTest()
    {
        $obj = new OfficialJson(
            Fest::findOne(['id' => 2]),
            file_get_contents(__DIR__ . '/../db/sqls/01/2ndfest/data/results.20150703171806.json')
        );
        
        if ($obj->sha256sum !== 'XNLnndL5YOVClyYIqqoKisBbty82S+74mtMFYlr6lbg=') {
            echo "SHA256SUM mismatch\n";
            echo "  Expect: XNLnndL5YOVClyYIqqoKisBbty82S+74mtMFYlr6lbg=\n";
            echo "  Actial: " . $obj->sha256sum . "\n";
            exit(1);
        }

        $ret = $obj->getWinCounts();
        foreach(['red' => 38, 'green' => 57] as $color => $expect) {
            if ($ret->$color !== $expect) {
                echo "WinCount mismatch ($color)\n";
                echo "  Expect: " . $expect . "\n";
                echo "  Actual: " . $ret->$color . "\n";
                exit(1);
            }
        }

        echo "OK\n";
    }

    public function actionUpdate()
    {
        $debug = false;

        $now = $debug ? strtotime('2015-07-03 17:18:06+9') : time();
        $fest = $this->getCurrentFest($now);
        if (!$fest) {
            echo "fest closed.\n";
            return 0;
        }

        $json = $debug
            ? file_get_contents(__DIR__ . '/../db/sqls/01/2ndfest/data/results.20150703171806.json')
            : $this->fetchJsonFromNintendo();
        if (!$json || substr($json, 0, 2) === '[]' || substr($json, 0, 2) === '{}') {
            echo "failed or empty json.\n";
            return 1;
        }

        $jsonObj = new OfficialJson($fest, $json);
        if (!$debug && $this->isDuplicated($fest, $jsonObj->sha256sum)) {
            echo "duplicated.\n";
            return 0;
        }

        $this->saveJson($fest, $json, $now);

        $winCounts = $jsonObj->getWinCounts();
        if ($winCounts->red < 1 && $winCounts->green < 1) {
            echo "winCounts error\n";
            return 1;
        }

        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            $modelOfficialData = new OfficialData();
            $modelOfficialData->fest_id = $fest->id;
            $modelOfficialData->sha256sum = $jsonObj->sha256sum;
            $modelOfficialData->downloaded_at = $now;
            if (!$modelOfficialData->save()) {
                echo "official_data save failed\n";
                throw new \Exception();
            }

            $modelWinData = new OfficialWinData();
            $modelWinData->data_id = $modelOfficialData->id;
            $modelWinData->color_id = 1;
            $modelWinData->count = $winCounts->red;
            if (!$modelWinData->save()) {
                echo "official_win_data save failed (red)\n";
                throw new \Exception();
            }

            $modelWinData = new OfficialWinData();
            $modelWinData->data_id = $modelOfficialData->id;
            $modelWinData->color_id = 2;
            $modelWinData->count = $winCounts->green;
            if (!$modelWinData->save()) {
                echo "official_win_data save failed (green)\n";
                throw new \Exception();
            }

            $transaction->commit();
            echo "OK\n";
            return 0;
        } catch (\Exception $e) {
        }
        $transaction->rollback();
        return 2;
    }

    private function getCurrentFest($now)
    {
        return Fest::find()
            ->andWhere(['<=', 'fest.start_at', $now])
            ->andWhere(['>', 'fest.end_at', $now])
            ->one();
    }

    private function fetchJsonFromNintendo()
    {
        $url = 'http://s3-ap-northeast-1.amazonaws.com/splatoon-data.nintendo.net/recent_results.json';
        $curl = new Curl();
        $curl->setUserAgent('fest.ink (+https://fest.ink/)');
        $ret = $curl->get($url, ['_' => time()]);
        return $curl->error ? false : (string)$ret;
    }

    private function isDuplicated(Fest $fest, $sha256sum)
    {
        return !!OfficialData::findOne([
            'fest_id' => $fest->id,
            'sha256sum' => $sha256sum,
        ]);
    }

    private function saveJson(Fest $fest, $json, $fetchedAt)
    {
        $filepath = __DIR__ . '/../runtime/official-data/fest-' . $fest->id . '/' . date('Y-m-d\TH-i-s', $fetchedAt) . '.json';
        if (!file_exists(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }
        file_put_contents($filepath, $json);
    }
}
