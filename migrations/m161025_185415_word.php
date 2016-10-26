<?php

use yii\db\Migration;

/**
 * Для корректной работы в этой табличке id должны идти последовательно
 */
class m161025_185415_word extends Migration
{
    public function up()
    {
        $this->createTable('word', [
            'id' => $this->primaryKey(),
            'rus' => $this->string()->notNull(),
            'eng' => $this->string()->notNull(),
        ]);

        $this->insert('word', ['rus' => 'яблоко', 'eng' => 'apple']);
        $this->insert('word', ['rus' => 'персик', 'eng' => 'pear']);
        $this->insert('word', ['rus' => 'апельсин', 'eng' => 'orange']);
        $this->insert('word', ['rus' => 'виноград', 'eng' => 'grape']);
        $this->insert('word', ['rus' => 'лимон', 'eng' => 'lemon']);
        $this->insert('word', ['rus' => 'ананас', 'eng' => 'pineapple']);
        $this->insert('word', ['rus' => 'арбуз', 'eng' => 'watermelon']);
        $this->insert('word', ['rus' => 'кокос', 'eng' => 'coconut']);
        $this->insert('word', ['rus' => 'банан', 'eng' => 'banana']);
        $this->insert('word', ['rus' => 'помело', 'eng' => 'pomelo']);
        $this->insert('word', ['rus' => 'клубника', 'eng' => 'strawberry']);
        $this->insert('word', ['rus' => 'малина', 'eng' => 'raspberry']);
        $this->insert('word', ['rus' => 'дыня', 'eng' => 'melon']);
        $this->insert('word', ['rus' => 'абрикос', 'eng' => 'apricot']);
        $this->insert('word', ['rus' => 'манго', 'eng' => 'mango']);
        $this->insert('word', ['rus' => 'слива', 'eng' => 'pear']);
        $this->insert('word', ['rus' => 'гранат', 'eng' => 'pomegranate']);
        $this->insert('word', ['rus' => 'вишня', 'eng' => 'cherry']);
    }

    public function down()
    {
        $this->dropTable('word');
    }
}
