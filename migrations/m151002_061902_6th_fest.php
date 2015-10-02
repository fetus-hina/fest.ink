<?php
use yii\db\Migration;

class m151002_061902_6th_fest extends Migration
{
    public function safeUp()
    {
        $this->insert('fest', [
            'id'        => 6,
            'name'      => 'イカ vs タコ',
            'start_at'  => strtotime('2015-10-10 09:00:00+09:00'),
            'end_at'    => strtotime('2015-10-11 09:00:00+09:00'),
        ]);
        $this->batchInsert(
            'team',
            [ 'fest_id', 'color_id', 'name', 'keyword', 'ink_color' ],
            [
                [
                    'fest_id' => 6,
                    'color_id' => 1,
                    'name' => 'イカ',
                    'keyword' => 'イカ',
                    'ink_color' => '59a2b3',
                ],
                [
                    'fest_id' => 6,
                    'color_id' => 2,
                    'name' => 'タコ',
                    'keyword' => 'タコ',
                    'ink_color' => 'd92b42',
                ],
            ]
        );
    }

    public function safeDown()
    {
        $this->delete('team', 'team.fest_id = :id', [':id' => 6]);
        $this->delete('fest', 'fest.id = :id', [':id' => 6]);
    }
}
