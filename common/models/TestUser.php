<?php
/**
 * Created by PhpStorm.
 * User: sergio
 * Date: 25.01.16
 * Time: 18:31
 */

namespace common\models;

use Faker\Provider\cs_CZ\DateTime;
use Yii;
use yii\i18n\Formatter;

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

    const STATUS_DEFAULT_NAME = 'зарегистрирован';

    /**
     * статус пользователя: на рассмотрении
     */
    const STATUS_UNDER_CONSIDERATION = 1;

    const STATUS_UNDER_CONSIDERATION_NAME = 'на рассмотрении';

    /**
     * статус пользователя: принят (на работу)
     */
    const STATUS_ACCEPTED = 2;

    const STATUS_ACCEPTED_NAME = 'принят';

    /**
     * статус пользователя: отклонён
     */
    const STATUS_DECLINED = 3;

    const STATUS_DECLINED_NAME = 'отклонён';

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

    public static $userStatusText = [

        self::STATUS_DEFAULT => 'кандидат ещё не рассматривался',
        self::STATUS_UNDER_CONSIDERATION => 'кандидат находится  в стадии рассмотрения',
        self::STATUS_ACCEPTED => 'принятно решение принять кандидата на работу',
        self::STATUS_DECLINED =>'принятно решение отклонить кандидата',

    ];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'surname', 'patronymic', 'phone', 'date_of_birth' ], 'required'],
            [['status'], 'integer', 'integerOnly' => true],
            [['status'], 'in', 'range' => range(self::STATUS_DEFAULT,self::STATUS_DECLINED)],
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

            $this->addError($attribute, 'указанная дата рождения соответствует возрасту менее '.self::MINIMUM_AGE. ' лет');
        }
    }

    public static function getAllStatuses(){

        return [
            self::STATUS_DEFAULT => self::STATUS_DEFAULT_NAME,
            self::STATUS_UNDER_CONSIDERATION => self::STATUS_UNDER_CONSIDERATION_NAME,
            self::STATUS_ACCEPTED => self::STATUS_ACCEPTED_NAME,
            self::STATUS_DECLINED => self::STATUS_DECLINED_NAME,

        ];

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
        if ($this->isNewRecord) {
            $this->created = (new \DateTime())->format('Y-m-d H:i:s');
            $this->phone = self::clearPhone($this->phone);
        }


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