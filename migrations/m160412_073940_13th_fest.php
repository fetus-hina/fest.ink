<?php
use yii\db\Migration;

class m160412_073940_13th_fest extends Migration
{
    public function safeUp()
    {
        $this->insert('fest', [
            'id'        => 13,
            'name'      => 'ツナマヨネーズ vs 紅しゃけ',
            'start_at'  => strtotime('2016-04-23 09:00:00+09:00'),
            'end_at'    => strtotime('2016-04-24 09:00:00+09:00'),
        ]);
        $this->batchInsert(
            'team',
            [ 'fest_id', 'color_id', 'name', 'keyword', 'ink_color' ],
            [
                [
                    'fest_id' => 13,
                    'color_id' => 1,
                    'name' => 'ツナマヨネーズ',
                    'keyword' => 'ツナマヨ',
                    'ink_color' => '2cbac0',
                ],
                [
                    'fest_id' => 13,
                    'color_id' => 2,
                    'name' => '紅しゃけ',
                    'keyword' => 'しゃけ',
                    'ink_color' => 'da5479',
                ],
            ]
        );
    }

    public function safeDown()
    {
        $this->delete('team', 'team.fest_id = :id', [':id' => 13]);
        $this->delete('fest', 'fest.id = :id', [':id' => 13]);
    }
}
