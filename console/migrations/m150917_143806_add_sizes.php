<?php

use yii\db\Migration;

class m150917_143806_add_sizes extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%size}}', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer()->notNull(),
            'size' => $this->string()->notNull()
        ], $tableOptions);
        
        $this->dropColumn('{{%product}}', 'size');
        
        $this->addForeignKey('size_product', '{{%size}}', 'product_id', '{{%product}}', 'id');
    }

    public function safeDown()
    {
        $this->dropForeignKey('size_product', '{{%size}}');
        $this->dropTable('{{%size}}');
        $this->addColumn('{{%product}}', 'size', $this->string());
    }
}
