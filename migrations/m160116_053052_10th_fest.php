<?php
use yii\db\Migration;

class m160116_053052_10th_fest extends Migration
{
    public function safeUp()
    {
        $this->insert('fest', [
            'id'        => 10,
            'name'      => 'カンペキなカラダ vs カンペキな頭脳',
            'start_at'  => strtotime('2016-01-23 12:00:00+09:00'),
            'end_at'    => strtotime('2016-01-24 12:00:00+09:00'),
        ]);
        $this->batchInsert(
            'team',
            [ 'fest_id', 'color_id', 'name', 'keyword', 'ink_color' ],
            [
                [
                    'fest_id' => 10,
                    'color_id' => 1,
                    'name' => 'カンペキなカラダ',
                    'keyword' => 'カラダ',
                    'ink_color' => '95cb00',
                ],
                [
                    'fest_id' => 10,
                    'color_id' => 2,
                    'name' => 'カンペキな頭脳',
                    'keyword' => '頭脳',
                    'ink_color' => 'ea2e01',
                ],
            ]
        );
    }

    public function safeDown()
    {
        $this->delete('team', 'team.fest_id = :id', [':id' => 10]);
        $this->delete('fest', 'fest.id = :id', [':id' => 10]);
    }
}
