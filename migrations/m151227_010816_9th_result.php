<?php
use yii\db\Migration;

class m151227_010816_9th_result extends Migration
{
    public function safeUp()
    {
        $this->insert('official_result', [
            'fest_id' => 9,
            'alpha_people' => 62,
            'bravo_people' => 38,
            'alpha_win' => 44,
            'bravo_win' => 56,
            'win_rate_times' => 6,
        ]);
    }

    public function safeDown()
    {
        $this->delete('official_result', ['fest_id' => 9]);
    }
}
