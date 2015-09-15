<?php

use yii\db\Migration;

class m130524_201442_init extends Migration
{
    use \common\traits\MigrationExtended;
    
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%product}}', [
            'id' => $this->primaryKey(),
            'section' => $this->string()->notNull(),
            'subsection' => $this->string()->notNull(),
            'article' => $this->string(),
            'brand' => $this->string()->notNull(),
            'model' => $this->string()->notNull(),
            'name' => $this->string()->notNull(),
            'size' => $this->string(),
            'color' => $this->string(),
            'orientation' => $this->enum(['L', 'R'])
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%product}}');
    }
}
