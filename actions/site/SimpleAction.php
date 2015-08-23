<?php
namespace app\actions\site;

use yii\web\ViewAction as BaseAction;

class SimpleAction extends BaseAction
{
    public $view = false;

    public function run()
    {
        return $this->controller->render($this->view);
    }
}
