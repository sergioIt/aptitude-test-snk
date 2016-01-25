<?php
/**
 * Created by PhpStorm.
 * User: sergio
 * Date: 25.01.16
 * Time: 18:31
 */

namespace app\models;

use Yii;

/**
 * This is the model class for table "test_user".
 *
 * @property integer $id
 * @property string $name
 * @property string $surname
 * @property string $patronymic
 * @property string $phone
 * @property string $date_of_birth
 * @property integer $status
 * @property string $created
 */
class TestUser extends \yii\db\ActiveRecord
{
    const DATE_DB_FORMAT_FOR_VALIDATOR = 'yyyy-MM-dd HH:mm:ss';

    /**
     * ещё не прошёл тест, но уже начал
     */
    const STATUS_NOT_FINISHED = 0;
    /**
     * прошёл тест
     */
    const STATUS_FINISHED = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'test_user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'surname', 'patronymic', 'phone', 'date_of_birth' ], 'required'],
            //[['date_of_birth', 'created'], 'safe'],
            [['status'], 'integer'],
            [['name'], 'string', 'max' => 100],
            [['surname', 'patronymic'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 12],
            [['phone'], 'unique'],
            [   'created',
                'date',
                'format' => self::DATE_DB_FORMAT_FOR_VALIDATOR
            ],
            [   'date_of_birth',
                'date',
                'format' => 'yyyy-MM-dd'
            ]

        ];
    }

    public function beforeSave(){

        $date = (new \DateTime())->format('Y-m-d H:i:s');
        var_dump($date);
        $this->created = $date;

        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Имя',
            'surname' => 'Фамилия',
            'patronymic' => 'Отчество',
            'phone' => 'телефон',
            'date_of_birth' => 'Дата рождения',
            'status' => 'Статус',
            'created' => 'Зарегистрирован',
        ];
    }
}