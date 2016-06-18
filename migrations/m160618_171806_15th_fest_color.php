<?php
use yii\db\Migration;

class m160618_171806_15th_fest_color extends Migration
{
    public function safeUp()
    {
        $this->update(
            'team',
            [ 'ink_color' => 'e6ab2e' ],
            [ 'fest_id' => 15, 'color_id' => 1 ]
        );
        $this->update(
            'team',
            [ 'ink_color' => '3ccc14' ],
            [ 'fest_id' => 15, 'color_id' => 2 ]
        );
    }

    public function safeDown()
    {
        $this->update(
            'team',
            [ 'ink_color' => 'fce802' ],
            [ 'fest_id' => 15, 'color_id' => 1 ]
        );
        $this->update(
            'team',
            [ 'ink_color' => 'c0e022' ],
            [ 'fest_id' => 15, 'color_id' => 2 ]
        );
    }
}
