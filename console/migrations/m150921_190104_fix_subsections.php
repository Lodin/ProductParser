<?php

use yii\db\Migration;

class m150921_190104_fix_subsections extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('{{%product}}', 'subsection', $this->string());
    }

    public function safeDown()
    {
        $this->alterColumn('{{%product}}', 'subsection', $this->string()->notNull());
    }
}
