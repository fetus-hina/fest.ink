<?php
use yii\db\Migration;

class m160621_124528_15th_result extends Migration
{
    public function safeUp()
    {
        $this->insert('official_result', [
            'fest_id' => 15,
            'alpha_people' => 37,
            'bravo_people' => 63,
            'alpha_win' => 52,
            'bravo_win' => 48,
            'win_rate_times' => 6,
        ]);
    }

    public function safeDown()
    {
        $this->delete('official_result', ['fest_id' => 15]);
    }
}
