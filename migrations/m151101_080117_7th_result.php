<?php
use yii\db\Migration;

class m151101_080117_7th_result extends Migration
{
    public function safeUp()
    {
        $this->insert('official_result', [
            'fest_id' => 7,
            'alpha_people' => 47,
            'bravo_people' => 53,
            'alpha_win' => 51,
            'bravo_win' => 49,
            'win_rate_times' => 6,
        ]);
    }

    public function safeDown()
    {
        $this->delete('official_result', ['fest_id' => 7]);
    }
}
