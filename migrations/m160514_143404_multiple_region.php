<?php
use yii\db\Migration;

class m160514_143404_multiple_region extends Migration
{
    public function up()
    {
        $this->execute(
            'ALTER TABLE {{fest}} ADD COLUMN [[is_multiple_region]] INTEGER NOT NULL DEFAULT 0'
        );
        $this->update('fest', ['is_multiple_region' => 1], ['id' => 14]);
    }

    public function down()
    {
        return false;
    }
}
