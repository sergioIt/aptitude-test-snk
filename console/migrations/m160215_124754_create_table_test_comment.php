<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * Создаёт таблицу для комментриев к тесту
 * предполагается, что разные пользователи бэкенда могут добавлять сколько угодно комментов к одному тесту
 *
 * редактировать свои комменты нельзя (для простоты), можно только добалять новые
 *
 * Class m160215_124754_create_table_test_comment
 */
class m160215_124754_create_table_test_comment extends Migration
{

    const TABLE_COMMENTS = 'test_comment';
    const TABLE_USER = 'user';
    const TABLE_TEST = 'test';

    private $foreignKeyUser = [
        'name' => 'fk_comment_user_id_to_user',
        'table'=>self::TABLE_COMMENTS,
        'column' => 'user_id',
        'ref_table' => self::TABLE_USER,
        'ref_column' => 'id'
    ];

    private $foreignKeyTest = [
        'name' => 'fk_comment_test_id_to_test',
        'table'=>self::TABLE_COMMENTS,
        'column' => 'test_id',
        'ref_table' => self::TABLE_TEST,
        'ref_column' => 'id'
    ];

    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';

        $this->createTable(self::TABLE_COMMENTS, [
            'id' => $this->primaryKey()->notNull(),
            //id теста
            'test_id' => $this->integer()->notNull(),
            // id пользовтеля бэкенда
            'user_id' => $this->integer()->notNull(),
            //текст коммента
            'text' => $this->text()->notNull(),
            // дата создания
            'created' =>  $this->dateTime()->notNull(),

        ], $tableOptions);


        // добавляем внешний ключ на id пользователя в тесте
        $this->addForeignKey(
            $this->foreignKeyUser['name'],
            $this->foreignKeyUser['table'],
            $this->foreignKeyUser['column'],
            $this->foreignKeyUser['ref_table'],
            $this->foreignKeyUser['ref_column']
        );

        // добавляем внешний ключ на id теста
        $this->addForeignKey(
            $this->foreignKeyTest['name'],
            $this->foreignKeyTest['table'],
            $this->foreignKeyTest['column'],
            $this->foreignKeyTest['ref_table'],
            $this->foreignKeyTest['ref_column']
        );


    }

    public function down()
    {
 /*       echo 'drop foreign key '.  $this->foreignKeyUser['name'] ."\n";

        $this->dropForeignKey( $this->foreignKeyUser['name'], $this->foreignKeyUser['table']);

        echo 'drop foreign key '.  $this->foreignKeyTest['name'] ."\n";

        $this->dropForeignKey( $this->foreignKeyTest['name'], $this->foreignKeyTest['table']);*/

        echo 'drop table'. self::TABLE_COMMENTS ."\n";

        $this->dropTable(self::TABLE_COMMENTS);

        return true;
    }


}
