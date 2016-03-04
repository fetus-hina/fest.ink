<?php
use yii\db\Migration;

class m160304_104135_12th_fest extends Migration
{
    public function safeUp()
    {
        $this->insert('fest', [
            'id'        => 12,
            'name'      => 'ガンガンいこうぜ vs いのちだいじに',
            'start_at'  => strtotime('2016-03-12 09:00:00+09:00'),
            'end_at'    => strtotime('2016-03-13 09:00:00+09:00'),
        ]);
        $this->batchInsert(
            'team',
            [ 'fest_id', 'color_id', 'name', 'keyword', 'ink_color' ],
            [
                [
                    'fest_id' => 12,
                    'color_id' => 1,
                    'name' => 'ガンガンいこうぜ',
                    'keyword' => 'ガンガン',
                    'ink_color' => 'ea0041',
                ],
                [
                    'fest_id' => 12,
                    'color_id' => 2,
                    'name' => 'いのちだいじに',
                    'keyword' => 'いのち',
                    'ink_color' => 'edd301',
                ],
            ]
        );
    }

    public function safeDown()
    {
        $this->delete('team', 'team.fest_id = :id', [':id' => 12]);
        $this->delete('fest', 'fest.id = :id', [':id' => 12]);
    }
}
