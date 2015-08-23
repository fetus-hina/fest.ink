<?php
namespace app\controllers;

use Yii;
use app\components\web\Controller;

class SiteController extends Controller
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
                'view' => 'error.tpl',
            ],
            'privacy' => [
                'class' => 'app\actions\site\SimpleAction',
                'view' => 'privacy.tpl',
            ],
        ];
    }
}
