<?php
use yii\db\Migration;

class m160610_132758_15th_fest extends Migration
{
    public function safeUp()
    {
        $this->insert('fest', [
            'id'        => 15,
            'name'      => 'きのこの山 vs たけのこの里',
            'start_at'  => strtotime('2016-06-18 09:00:00+09:00'),
            'end_at'    => strtotime('2016-06-19 09:00:00+09:00'),
        ]);
        $this->batchInsert(
            'team',
            [ 'fest_id', 'color_id', 'name', 'keyword', 'ink_color' ],
            [
                [
                    'fest_id' => 15,
                    'color_id' => 1,
                    'name' => 'きのこの山',
                    'keyword' => 'きのこ',
                    'ink_color' => 'fce802',
                ],
                [
                    'fest_id' => 15,
                    'color_id' => 2,
                    'name' => 'たけのこの里',
                    'keyword' => 'たけのこ',
                    'ink_color' => 'c0e022',
                ],
            ]
        );
    }

    public function safeDown()
    {
        $this->delete('team', ['fest_id' => 15]);
        $this->delete('fest', ['id' => 15]);
    }
}
