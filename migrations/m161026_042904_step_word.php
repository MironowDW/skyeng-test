<?php

use yii\db\Migration;

class m161026_042904_step_word extends Migration
{
    public function up()
    {
        $this->createTable('step_word', [
            'id' => $this->primaryKey(),
            'stepId' => $this->integer()->notNull(),
            'wordId' => $this->integer()->notNull(),
            'isBase' => $this->boolean()->defaultValue(0),
        ]);
    }

    public function down()
    {
        $this->dropTable('step_word');
    }
}
