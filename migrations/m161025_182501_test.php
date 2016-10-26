<?php

use yii\db\Migration;

class m161025_182501_test extends Migration
{
    public function up()
    {
        $this->createTable('test', [
            'id' => $this->primaryKey(),
            'userId' => $this->integer()->notNull(),
            'status' => $this->integer()->defaultValue(0),
        ]);
    }

    public function down()
    {
        $this->dropTable('test');
    }
}
