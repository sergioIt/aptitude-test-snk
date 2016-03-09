<?php
/**
 * Created by PhpStorm.
 * User: sergio
 * Date: 04.03.16
 * Time: 13:34
 *
 * Контроллер, управляеющий отчётами по тестам и их рассылкой
 * Основное назаначение - ежедневная рассылка уведомлений по email о том, сколько тестов было пройдено за предыдущие сутки
 *
 */

namespace console\controllers;

use common\models\Test;

use yii\console\Controller;

class ReportController extends Controller
{

    public function actionSend()
    {

        // выбор тестов за прошлые сутки
        $recentTests = $this->getRecentTests();

        // если есть тесты за прошлые сутки, то отправляем письмо-уведомление
        if (!empty ($recentTests)) {

            $notifier = \Yii::$app->params['notifier'];

            $transport = \Swift_SmtpTransport::newInstance(
                $notifier['smtp_server'], $notifier['smtp_port'], $notifier['ssl'])
                ->setUsername($notifier['login'])
                ->setPassword($notifier['password']);

            // Create the Mailer using your created Transport
            $mailer = \Swift_Mailer::newInstance($transport);

            // Create a message
            $message = \Swift_Message::newInstance($this->composeSubject())

                ->setFrom(array( $notifier['from'] => 'SNK Notifier'));

           foreach($notifier['destinations'] as $mail){

                $message->setTo($mail);
            }

                $message->setBody($this->composeMessage($recentTests))
                ->setContentType('text/html')
                ->setCharset('UTF-8');

            $result = $mailer->send($message);

            if($result == 1){

                echo 'send notify success' . "\n" ;
                return 0;
            }
            else{
                echo 'send fail' . "\n" ;
                return 2;
            }

        }
        echo 'no recent results found '. "\n" ;
        return 1;
    }

    /**
     * Получает тесты за прошлый день
     * @return array|\yii\db\ActiveRecord[]
     */
    private function getRecentTests()
    {

        $now = new \DateTime();

        $yesterday = $now->modify('-1 day');
        $from = $yesterday->format('Y-m-d');
        $to = $yesterday->setTime(23, 59, 59)->format('Y-m-d H:i:s');

        return Test::find()->with('user')->where(['between', 'created', $from, $to])->asArray()->all();
    }

    /**
     * Компонует текст сообщения в письме-уведомлении
     *
     * @param $tests
     * @return string
     */
    private function composeMessage($tests)
    {
        $message = '';

        $message .= $this->composeSubject();

        $message .= '<br> Новые тесты: <br>';

        foreach ($tests as $test) {
            $testInfo = 'id: ' . $test['id'] . ' создан:' . $test['created'] . ' общий балл:' . $test['score'] . ' Имя: ' . $test['user']['name'];

            $message .= '<br>';
            $message .= $testInfo;
        }
        return $message;
    }

    /**
     * Создаёт тему письма-уведолмения
     * @return string
     */
    private function composeSubject()
    {

        $now = new \DateTime();

        $yesterday = $now->modify('-1 day')->format('d.m.Y');

        return 'Входное онлайн-тестирование. Отчёт за ' . $yesterday;
    }
}