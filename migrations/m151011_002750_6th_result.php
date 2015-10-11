<?php
use yii\db\Migration;

class m151011_002750_6th_result extends Migration
{
    public function safeUp()
    {
        $this->insert('official_result', [
            'fest_id' => 6,
            'alpha_people' => 66,
            'bravo_people' => 34,
            'alpha_win' => 47,
            'bravo_win' => 53,
            'win_rate_times' => 4,
        ]);
    }

    public function safeDown()
    {
        $this->delete('official_result', ['fest_id' => 6]);
    }
}
