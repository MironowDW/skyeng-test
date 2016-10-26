<?php

use yii\db\Migration;

class m161025_183041_step extends Migration
{
    public function up()
    {
        $this->createTable('step', [
            'id' => $this->primaryKey(),
            'testId' => $this->integer()->notNull(),
            'wordId' => $this->integer()->notNull(),
            'direction' => $this->string()->notNull(),
            'status' => $this->integer()->defaultValue(0),
        ]);
    }

    public function down()
    {
        $this->dropTable('step');
    }
}
