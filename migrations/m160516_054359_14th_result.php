<?php
use yii\db\Migration;

class m160516_054359_14th_result extends Migration
{
    public function safeUp()
    {
        $this->insert('official_result', [
            'fest_id' => 14,
            'alpha_people' => 52,
            'bravo_people' => 48,
            'alpha_win' => 51,
            'bravo_win' => 49,
            'win_rate_times' => 6,
        ]);
    }

    public function safeDown()
    {
        $this->delete('official_result', ['fest_id' => 14]);
    }
}
