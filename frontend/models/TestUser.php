<?php
/**
 * Created by PhpStorm.
 * User: sergio
 * Date: 25.01.16
 * Time: 18:31
 */

namespace app\models;

use Faker\Provider\cs_CZ\DateTime;
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
     * формат даты в базе данных
     */
    const DATE_DB_FORMAT = 'Y-m-d';

    /**
     * статус пользователя: зарегистрировался, перешёл к тесту
     */
    const STATUS_DEFAULT = 0;
    /**
     * статус пользователя: ответил на часть вопросов
     */
    const STATUS_NOT_FINISHED = 1;

    /**
     * статус пользователя: ответил на все вопросы
     */
    const STATUS_FINISHED = 2;

    /**
     * минимально допустимый возраст кандидата
     */
    const MINIMUM_AGE = 18;
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
            [['status'], 'integer'],
            [['name'], 'string', 'max' => 100],
            [['surname', 'patronymic'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 16],
            [['phone'], 'unique'],
            [   'created',
                'date',
                'format' => self::DATE_DB_FORMAT_FOR_VALIDATOR
            ],
            [   'date_of_birth',
                'date',
                'format' => 'yyyy-MM-dd'
            ],
            ['date_of_birth','isNotTooYoung']


        ];
    }

    /**
     * Проверяет по дате рождения, не слишком ли молод кандидат
     * @param $attribute
     */
    public function isNotTooYoung($attribute){

        $now = new \DateTime();
        $dateOfBirth = \DateTime::createFromFormat(self::DATE_DB_FORMAT, $this->$attribute);

        $diff = $now->diff($dateOfBirth);

        if($diff->format('%y') < self::MINIMUM_AGE){

            $this->addError($attribute, 'Слишком молодой');
        }
    }

    /**
     * Очищает номер, оставляя только цифры
     * @param $phone
     * @return mixed
     */
    private function clearPhone($phone){
        return preg_replace('/\D/', '', $phone);
    }

    /**
     * Перед сохранением модели в базу:
     * добавляем дату создания и оцищаем телефон
     *
     * @return bool
     */
    public function beforeSave()
    {
        $this->created = (new \DateTime())->format('Y-m-d H:i:s');
        $this->phone = self::clearPhone($this->phone);
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