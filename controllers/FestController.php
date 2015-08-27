<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\controllers;

use Yii;
use app\components\web\Controller;
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
