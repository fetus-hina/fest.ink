<?php
use yii\db\Migration;

class m160229_113002_11th_result extends Migration
{
    public function safeUp()
    {
        $this->insert('official_result', [
            'fest_id' => 11,
            'alpha_people' => 61,
            'bravo_people' => 39,
            'alpha_win' => 36,
            'bravo_win' => 64,
            'win_rate_times' => 6,
        ]);
    }

    public function safeDown()
    {
        $this->delete('official_result', ['fest_id' => 11]);
    }
}
