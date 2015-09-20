<?php

use common\models\db\Color;
use yii\db\Migration;

class m150917_141259_add_colors extends Migration
{
    use \common\traits\MigrationExtended;
    
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%color}}', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer()->notNull(),
            'name' => $this->enum([
                Color::RED,
                Color::BLACK,
                Color::WHITE,
                Color::GOLD,
                Color::BLUE,
                Color::YELLOW,
                Color::DARK_BLUE
            ])->notNull()
        ], $tableOptions);
        
        $this->dropColumn('{{%product}}', 'color');
        
        $this->addForeignKey('color_product', '{{%color}}', 'product_id', '{{%product}}', 'id');
    }

    public function safeDown()
    {
        $this->dropForeignKey('color_product', '{{%color}}');
        $this->dropTable('{{%color}}');
        $this->addColumn('{{%product}}', 'name', $this->string());
    }
}
