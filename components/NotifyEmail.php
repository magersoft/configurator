<?php
/**
 * Created by PhpStorm.
 * User: mager
 * Date: 07.03.2019
 * Time: 20:27
 */

namespace app\components;

use Yii;

class NotifyEmail extends \yii\base\Component
{
    private $setTo;

    private $setSubject;

    private function sendEmail($model, ...$args)
    {

    }

    public static function sendParserInfo(...$data)
    {
        $view = 'mail-parser';
        $mailer = Yii::$app->mailer;
        $mailer->viewPath = '@app/mail';

        $sender = Yii::$app->params['adminEmail'];

        return $mailer->compose([
            'html' => $view,
            'text' => './text/' . $view
        ], ['data' => $data])
            ->setTo('magervlad@yandex.ru')
            ->setFrom($sender)
            ->setSubject('Parser Info')
            ->send();
    }
}