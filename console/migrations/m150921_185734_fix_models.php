<?php

use yii\db\Migration;

class m150921_185734_fix_models extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('{{%product}}', 'model', $this->string());
    }

    public function safeDown()
    {
        $this->alterColumn('{{%product}}', 'model', $this->string()->notNull());
    }
}
