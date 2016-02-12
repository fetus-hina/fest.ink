<?php
use yii\db\Migration;

class m160212_113001_11th_fest extends Migration
{
    public function safeUp()
    {
        $this->insert('fest', [
            'id'        => 11,
            'name'      => 'ポケットモンスター赤 vs ポケットモンスター緑',
            'start_at'  => strtotime('2016-02-20 06:00:00+09:00'),
            'end_at'    => strtotime('2016-02-21 06:00:00+09:00'),
        ]);
        $this->batchInsert(
            'team',
            [ 'fest_id', 'color_id', 'name', 'keyword', 'ink_color' ],
            [
                [
                    'fest_id' => 11,
                    'color_id' => 1,
                    'name' => 'ポケットモンスター赤',
                    'keyword' => '赤',
                    'ink_color' => 'e70012',
                ],
                [
                    'fest_id' => 11,
                    'color_id' => 2,
                    'name' => 'ポケットモンスター緑',
                    'keyword' => '緑',
                    'ink_color' => '07a33e',
                ],
            ]
        );
    }

    public function safeDown()
    {
        $this->delete('team', 'team.fest_id = :id', [':id' => 11]);
        $this->delete('fest', 'fest.id = :id', [':id' => 11]);
    }
}
