<?php
use yii\db\Migration;

class m160125_135253_10th_result extends Migration
{
    public function safeUp()
    {
        $this->insert('official_result', [
            'fest_id' => 10,
            'alpha_people' => 44,
            'bravo_people' => 56,
            'alpha_win' => 53,
            'bravo_win' => 47,
            'win_rate_times' => 6,
        ]);
    }

    public function safeDown()
    {
        $this->delete('official_result', ['fest_id' => 10]);
    }
}
