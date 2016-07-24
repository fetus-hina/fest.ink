<?php
use yii\db\Migration;

class m160724_101921_16th_result extends Migration
{
    public function safeUp()
    {
        $this->insert('official_result', [
            'fest_id' => 16,
            'alpha_people' => 46,
            'bravo_people' => 54,
            'alpha_win' => 49,
            'bravo_win' => 51,
            'win_rate_times' => 6,
        ]);
    }

    public function safeDown()
    {
        $this->delete('official_result', ['fest_id' => 16]);
    }
}
