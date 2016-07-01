<?php
use yii\db\Migration;

class m160701_123230_16th_fest extends Migration
{
    public function safeUp()
    {
        $this->insert('fest', [
            'id'        => 16,
            'name'      => 'アオリ vs ホタル',
            'start_at'  => strtotime('2016-07-22 12:00:00+09:00'),
            'end_at'    => strtotime('2016-07-24 12:00:00+09:00'),
            'is_multiple_region' => 1,
        ]);
        $this->batchInsert(
            'team',
            [ 'fest_id', 'color_id', 'name', 'keyword', 'ink_color' ],
            [
                [
                    'fest_id' => 16,
                    'color_id' => 1,
                    'name' => 'アオリ',
                    'keyword' => 'アオリ',
                    'ink_color' => 'b800af',
                ],
                [
                    'fest_id' => 16,
                    'color_id' => 2,
                    'name' => 'ホタル',
                    'keyword' => 'ホタル',
                    'ink_color' => '64c100',
                ],
            ]
        );
    }

    public function safeDown()
    {
        $this->delete('team', ['fest_id' => 16]);
        $this->delete('fest', ['id' => 16]);
    }
}
