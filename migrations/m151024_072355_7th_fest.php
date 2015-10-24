<?php
use yii\db\Migration;

class m151024_072355_7th_fest extends Migration
{
    public function safeUp()
    {
        $this->insert('fest', [
            'id'        => 7,
            'name'      => '愛 vs おカネ',
            'start_at'  => strtotime('2015-10-31 09:00:00+09:00'),
            'end_at'    => strtotime('2015-11-01 09:00:00+09:00'),
        ]);
        $this->batchInsert(
            'team',
            [ 'fest_id', 'color_id', 'name', 'keyword', 'ink_color' ],
            [
                [
                    'fest_id' => 7,
                    'color_id' => 1,
                    'name' => '愛',
                    'keyword' => '愛',
                    'ink_color' => 'e33781',
                ],
                [
                    'fest_id' => 7,
                    'color_id' => 2,
                    'name' => 'おカネ',
                    'keyword' => 'カネ',
                    'ink_color' => 'f6bf02',
                ],
            ]
        );
    }

    public function safeDown()
    {
        $this->delete('team', 'team.fest_id = :id', [':id' => 7]);
        $this->delete('fest', 'fest.id = :id', [':id' => 7]);
    }
}
