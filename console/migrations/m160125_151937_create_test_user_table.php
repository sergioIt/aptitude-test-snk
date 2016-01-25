<?php

use yii\db\Migration;

class m160125_151937_create_test_user_table extends Migration
{
    const TABLE_USER = 'test_user';

    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';

        $this->createTable(self::TABLE_USER, [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
            'surname' => $this->string()->notNull(),
            'patronymic' => $this->string()->notNull(),
            'phone' => $this->string(12)->notNull()->unique(),
            'date_of_birth' => $this->date()->notNull(),
            'status' => $this->smallInteger()->notNull()->defaultValue(0),
            'created' => $this->dateTime()->notNull(),
        ], $tableOptions);
    }

    public function down()
    {
        echo 'drop table'. self::TABLE_USER ."\n";

        $this->dropTable(self::TABLE_USER);

        return true;
    }

}
