<?php

use yii\db\Migration;

class m150921_190400_fix_brands extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('{{%product}}', 'brand', $this->string());
    }

    public function safeDown()
    {
        $this->alterColumn('{{%product}}', 'brand', $this->string()->notNull());
    }
}
