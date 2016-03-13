<?php
use yii\db\Migration;

class m160313_112900_12th_result extends Migration
{
    public function safeUp()
    {
        $this->insert('official_result', [
            'fest_id' => 12,
            'alpha_people' => 46,
            'bravo_people' => 54,
            'alpha_win' => 48,
            'bravo_win' => 52,
            'win_rate_times' => 6,
        ]);
    }

    public function safeDown()
    {
        $this->delete('official_result', ['fest_id' => 12]);
    }
}
