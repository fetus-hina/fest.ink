<?php
use yii\db\Migration;

class m151219_083107_9th_fest extends Migration
{
    public function safeUp()
    {
        $this->insert('fest', [
            'id'        => 9,
            'name'      => '第2回 赤いきつね vs 緑のたぬき',
            'start_at'  => strtotime('2015-12-26 09:00:00+09:00'),
            'end_at'    => strtotime('2015-12-27 09:00:00+09:00'),
        ]);
        $this->batchInsert(
            'team',
            [ 'fest_id', 'color_id', 'name', 'keyword', 'ink_color' ],
            [
                [
                    'fest_id' => 9,
                    'color_id' => 1,
                    'name' => '赤いきつね',
                    'keyword' => '赤いきつね',
                    'ink_color' => 'd9435f',
                ],
                [
                    'fest_id' => 9,
                    'color_id' => 2,
                    'name' => '緑のたぬき',
                    'keyword' => '緑のたぬき',
                    'ink_color' => '5cb85c',
                ],
            ]
        );
    }

    public function safeDown()
    {
        $this->delete('team', 'team.fest_id = :id', [':id' => 9]);
        $this->delete('fest', 'fest.id = :id', [':id' => 9]);
    }
}
