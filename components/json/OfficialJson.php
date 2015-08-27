<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components\json;

use Yii;
use yii\base\Component;
use app\models\Fest;

class OfficialJson extends Component
{
    private $fest;
    private $json;
    private $sha256sum;

    public function __construct(Fest $fest, $jsonText)
    {
        $this->fest = $fest;
        $this->json = json_decode($jsonText, true);
        $this->sha256sum = base64_encode(hash('sha256', $jsonText, true));
    }

    public function getSHA256Sum()
    {
        return $this->sha256sum;
    }

    public function getWinCounts()
    {
        $rTeam = $this->fest->redTeam;
        $gTeam = $this->fest->greenTeam;
        $rCount = 0;
        $gCount = 0;
        foreach ($this->json as $battle) {
            $winTeamName = $battle['win_team_name'];
            if (strpos($winTeamName, $rTeam->keyword) !== false) {
                ++$rCount;
            }
            if (strpos($winTeamName, $gTeam->keyword) !== false) {
                ++$gCount;
            }
        }
        return (object)[
            'red' => $rCount,
            'green' => $gCount,
        ];
    }
}
