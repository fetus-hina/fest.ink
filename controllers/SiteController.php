<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;

class SiteController extends Controller
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
                'view' => 'error.tpl',
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }
}
