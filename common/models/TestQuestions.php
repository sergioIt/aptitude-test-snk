<?php
/**
 * Created by PhpStorm.
 * User: sergio
 * Date: 27.01.16
 * Time: 15:37
 */

namespace common\models;

use Yii;

/**
 * This is the model class for table "test_questions".
 *
 * @property integer $id
 * @property string $text
 * @property integer $evaluative
 * @property integer $scale
 * @property integer $check_group
 * @property integer $multiple_answers
 * @property integer $min_options
 * @property integer $max_options
 * @property integer $custom_answer
 * @property integer $required
 *
 * @property TestAnswers[] $testAnswers
 */
class TestQuestions extends \yii\db\ActiveRecord
{
    /**
     * массив данный для шкалы (если вопрос подразумевает шкалу)
     * @var
     */
    public $scaleData;

    /**
     * Признак: подразумевает ли вопрос один вариант ответа из нескольких
     * (тогда нужно рендерить radioList)
     *
     * @var
     */
    public $radioList;

    /**
     * Признак: подразумевает ли вопрос несколько вариантов ответа из нескольких
     * (тогда нужно рендерить checkboxList)
     *
     * @var
     */
    public $checkboxList;

    /**
     * Ти элемента ответа при нескольких вариантах (radio/checkbox)
     * @var
     */
    public $listItemType;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'test_questions';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['text'], 'required'],
            [['text'], 'string'],
            // по умолчанию вопрос учитывается в подсчёте баллов
            ['evaluative', 'default','value' =>1],
            ['scale', 'integer','min' =>10, 'max' => 100],
            ['scale', 'default','value' =>null],

            ['check_group', 'integer', 'max' => 4],
            ['check_group', 'default','value' =>null],

            ['multiple_answers', 'integer', 'max' => 1],
            ['multiple_answers', 'default','value' =>null],

            ['min_options', 'integer', 'max' => 3],
            ['min_options', 'default','value' =>null],

            ['max_options', 'integer', 'max' => 3],
            ['max_options', 'default','value' =>null],

            ['custom_answer', 'integer', 'max' => 1],
            ['custom_answer', 'default','value' =>null],

            ['required', 'integer', 'min'=>0, 'max' => 1, 'integerOnly' => true],
            ['required', 'default','value' =>1],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'text' => 'Текст вопроса',
            'evaluative' => 'Оцениваемый',
            'scale' => 'Шкала ответа',
            'check_group' => 'Группа проверочных вопросов',
            'multiple_answers' => 'Подразумевает несколько вариантов ответов',
            'min_options' => 'Минимальное число ответов',
            'max_options' => 'Максимальное число ответов',
            'custom_answer' => 'Подразумеват только свой ответ',
            'required' => 'Ответ на вопрос обязателен',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnswers()
    {

     //  return $this->hasMany(TestAnswers::className(), ['question_id' => 'id']);

        return TestAnswers::find()->where(['question_id' => $this->id])->orderBy('id asc')->all();
    }

    /**
     * Подготовливает вопрос: навешиваем дополнительные атрибуты:
     * scaleData - массив для рендеринга шкалы в виде выпадающего списка
     */
    public function prepare(){

        //
        if(isset($this->scale)) {
            $scaleData = [];
            // определяем шаг шкалы
            // предполагается что максимальный балл шкалы может быть либо 10 либо 100
            // поэтому, разделив его на 10, получим шаг либо 1 либо 10
            $scaleStep = $this->scale/10;
            for($i = 0; $i <= $this->scale; $i += $scaleStep){
                $scaleData[] = $i;
            }
            $this->scaleData = $scaleData;
        }
        else{
            $this->scaleData = null;
        }

        // если вопрос имеет набор вариантов ответа, но не подразумевает нескольких ответов,
        // то считаем, что выводить нужно radioList
        if(! empty ($this->answers) && $this->multiple_answers == null){

            $this->radioList = 1;
            $this->listItemType = 'radio';
        }

        if(! empty ($this->answers) && $this->multiple_answers == 1){

            $this->checkboxList = 1;
            $this->listItemType = 'checkbox';
        }

        return $this;
    }
}
