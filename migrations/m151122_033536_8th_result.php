<?php
use yii\db\Migration;

class m151122_033536_8th_result extends Migration
{
    public function safeUp()
    {
        $this->insert('official_result', [
            'fest_id' => 8,
            'alpha_people' => 48,
            'bravo_people' => 52,
            'alpha_win' => 49,
            'bravo_win' => 51,
            'win_rate_times' => 6,
        ]);
    }

    public function safeDown()
    {
        $this->delete('official_result', ['fest_id' => 8]);
    }
}
