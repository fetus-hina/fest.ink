<?php
use yii\db\Migration;

class m160312_000348_12th_fix extends Migration
{
    public function safeUp()
    {
        $this->update(
            'team',
            ['keyword' => 'だいじに'],
            ['fest_id' => 12, 'color_id' => 2]
        );
    }

    public function safeDown()
    {
        $this->update(
            'team',
            ['keyword' => 'いのち'],
            ['fest_id' => 12, 'color_id' => 2]
        );
    }
}
