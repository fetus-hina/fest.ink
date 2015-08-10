<?php
namespace app\controllers;

use Yii;
use app\components\web\Controller;
use yii\filters\VerbFilter;

class TimezoneController extends Controller
{
    public $layout = "main.tpl";

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'set' => [ 'post' ],
                    '*' => [ 'get' ],
                ],
            ],
        ];
    }

    public function actions()
    {
        $prefix = 'app\actions\timezone';
        return [
            'json' => [ 'class' => $prefix . '\JsonAction' ],
            'set' => [ 'class' => $prefix . '\SetAction' ],
        ];
    }
}
