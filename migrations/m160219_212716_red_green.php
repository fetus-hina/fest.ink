<?php

use yii\db\Migration;

class m160219_212716_red_green extends Migration
{
    public function safeUp()
    {
        $this->update(
            'team',
            ['keyword' => 'レッド'],
            ['fest_id' => 11, 'color_id' => 1]
        );
        $this->update(
            'team',
            ['keyword' => 'グリーン'],
            ['fest_id' => 11, 'color_id' => 2]
        );
    }

    public function safeDown()
    {
        $this->update(
            'team',
            ['keyword' => '赤'],
            ['fest_id' => 11, 'color_id' => 1]
        );
        $this->update(
            'team',
            ['keyword' => '緑'],
            ['fest_id' => 11, 'color_id' => 2]
        );
    }
}
