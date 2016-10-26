<?php

use yii\db\Migration;

class m161025_185953_attempt extends Migration
{
    public function up()
    {
        $this->createTable('attempt', [
            'id' => $this->primaryKey(),
            'stepId' => $this->integer()->notNull(),
            'stepWordId' => $this->integer()->notNull(),
            'status' => $this->integer()->defaultValue(0),
        ]);
    }

    public function down()
    {
        $this->dropTable('attempt');
    }
}
