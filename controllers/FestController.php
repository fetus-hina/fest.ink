<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;

class FestController extends Controller
{
    public $layout = "main.tpl";

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    '*' => [ 'get' ],
                ],
            ],
        ];
    }

    public function actions()
    {
        $prefix = 'app\actions\fest';
        return [
            'index' => [ 'class' => $prefix . '\IndexAction' ],
            'json' => [ 'class' => $prefix . '\JsonAction' ],
            'view' => [ 'class' => $prefix . '\ViewAction' ],
        ];
    }
}
