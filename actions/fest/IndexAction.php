<?php
namespace app\actions\fest;

use Yii;
use yii\web\ViewAction as BaseAction;
use app\models\Fest;

class IndexAction extends BaseAction
{
    public function run()
    {
        return $this->controller->render('index.tpl', [
            'allFest' => Fest::find()->orderBy('fest.id DESC')->all(),
        ]);
    }
}
