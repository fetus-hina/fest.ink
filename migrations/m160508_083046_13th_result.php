<?php
use yii\db\Migration;

class m160508_083046_13th_result extends Migration
{
    public function safeUp()
    {
        $this->insert('official_result', [
            'fest_id' => 13,
            'alpha_people' => 48,
            'bravo_people' => 52,
            'alpha_win' => 51,
            'bravo_win' => 49,
            'win_rate_times' => 6,
        ]);
    }

    public function safeDown()
    {
        $this->delete('official_result', ['fest_id' => 13]);
    }
}
