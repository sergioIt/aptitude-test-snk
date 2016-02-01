<?php

//use yii\db\Schema;
use yii\db\Migration;

class m160201_114024_create_table_test extends Migration
{
    const TABLE_TEST = 'test';
    const TABLE_TEST_USER = 'test_user';

    private $foreignKeyUser = [
        'name' => 'fk_test_user_id_to_test_user',
        'table'=>self::TABLE_TEST,
        'column' => 'user_id',
        'ref_table' => self::TABLE_TEST_USER,
        'ref_column' => 'id'
    ];

    public function up()
    {
        //return true;

        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';

        echo 'create table'. self::TABLE_TEST ."\n";

        $this->createTable(self::TABLE_TEST, [
            'id' => $this->primaryKey()->notNull(),
            // кто проходит
            'user_id' => $this->integer()->notNull(),
            // дата создания (начала) теста
            'created' => $this->dateTime()->notNull(),
            // дата последнего редактирования теста
            'updated' => $this->dateTime()->notNull(),
            // статус теста - пройден частично (1) или полностью (2)
            'status' => 'tinyint unsigned not null',
            //сколько суммарно набрано баллов
            'score' => $this->smallInteger(),
            // поля для итогов по группам проверочных вопросов
            'check_group_1' => 'tinyint unsigned',
            'check_group_2' => 'tinyint unsigned',
            'check_group_3' => 'tinyint unsigned',

        ], $tableOptions);

        // добавляем внешний ключ на id пользователя в тесте
        $this->addForeignKey(
            $this->foreignKeyUser['name'],
            $this->foreignKeyUser['table'],
            $this->foreignKeyUser['column'],
            $this->foreignKeyUser['ref_table'],
            $this->foreignKeyUser['ref_column']
        );

    }

    public function down()
    {

        echo 'drop foreign key '.  $this->foreignKeyUser['name'] ."\n";

        $this->dropForeignKey( $this->foreignKeyUser['name'], $this->foreignKeyUser['table']);

        echo 'drop table'. self::TABLE_TEST ."\n";

        $this->dropTable(self::TABLE_TEST);

        return true;
    }


}
