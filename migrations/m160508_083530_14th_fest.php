<?php
use yii\db\Migration;

class m160508_083530_14th_fest extends Migration
{
    public function safeUp()
    {
        $this->insert('fest', [
            'id'        => 14,
            'name'      => 'オシャレなパーティー vs コスプレパーティー',
            'start_at'  => strtotime('2016-05-14 12:00:00+09:00'),
            'end_at'    => strtotime('2016-05-15 19:00:00+09:00'),
        ]);
        $this->batchInsert(
            'team',
            [ 'fest_id', 'color_id', 'name', 'keyword', 'ink_color' ],
            [
                [
                    'fest_id' => 14,
                    'color_id' => 1,
                    'name' => 'オシャレなパーティー',
                    'keyword' => 'シャレ',
                    'ink_color' => '14bca3',
                ],
                [
                    'fest_id' => 14,
                    'color_id' => 2,
                    'name' => 'コスプレパーティー',
                    'keyword' => 'コスプレ',
                    'ink_color' => 'fd9f02',
                ],
            ]
        );
    }

    public function safeDown()
    {
        $this->delete('team', ['fest_id' => 14]);
        $this->delete('fest', ['id' => 14]);
    }
}
