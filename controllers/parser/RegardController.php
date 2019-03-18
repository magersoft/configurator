<?php

namespace app\controllers\parser;

use app\models\Store;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleTor\Middleware;
use yii\filters\VerbFilter;
use yii\web\Controller;

class RegardController extends Controller
{
    const STORE_ID = Store::REGARD;

    private $url = 'https://www.regard.ru';

    private $api = 'ajax/filter2.0.php';

    private $torIp = '127.0.0.1:9150';

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'store' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('regard');
    }

    public function actionStore()
    {
        $stack = new HandlerStack();
        $stack->setHandler(new CurlHandler());
        $stack->push(Middleware::tor($this->torIp));

        $client = new Client([
            'handler' => $stack
        ]);

        $res = $client->request('GET', $this->url, ['http_errors' => false]);

        if ($res->getStatusCode() === 200) {
            $exist = Store::findOne(['id' => self::STORE_ID]);

            if (!$exist) {
                $model = new Store();
                $model->name = 'Regard';
                $model->url = $this->url;
                $model->save();
            }
            $exist->url = $this->url;
            $exist->save();
        } else {
            \Yii::error($res->getStatusCode());
        }

        return $this->render('regard', ['store' => $res->getStatusCode()]);
    }
}