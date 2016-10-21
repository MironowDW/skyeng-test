<?php

use yii\db\Migration;

class m161021_214222_user extends Migration
{
    public function up()
    {
        $this->createTable('user', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull(),
            'access_token' => $this->string()->notNull(),
        ]);
    }

    public function down()
    {
        $this->dropTable('user');
    }
}
