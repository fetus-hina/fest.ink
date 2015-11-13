<?php
use yii\db\Migration;

class m151113_085024_8th_fest extends Migration
{
    public function safeUp()
    {
        $this->insert('fest', [
            'id'        => 8,
            'name'      => '山の幸 vs 海の幸',
            'start_at'  => strtotime('2015-11-21 12:00:00+09:00'),
            'end_at'    => strtotime('2015-11-22 12:00:00+09:00'),
        ]);
        $this->batchInsert(
            'team',
            [ 'fest_id', 'color_id', 'name', 'keyword', 'ink_color' ],
            [
                [
                    'fest_id' => 8,
                    'color_id' => 1,
                    'name' => '山の幸',
                    'keyword' => '山',
                    'ink_color' => 'ec6782',
                ],
                [
                    'fest_id' => 8,
                    'color_id' => 2,
                    'name' => '海の幸',
                    'keyword' => '海',
                    'ink_color' => '2aab95',
                ],
            ]
        );
    }

    public function safeDown()
    {
        $this->delete('team', 'team.fest_id = :id', [':id' => 8]);
        $this->delete('fest', 'fest.id = :id', [':id' => 8]);
    }
}
