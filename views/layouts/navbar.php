<?php

declare(strict_types=1);

use app\models\Fest;
use yii\helpers\Html;

?>
<nav class="navbar navbar-inverse navbar-fixed-top">
  <div class="container-fluid">
    <div class="container">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="/">イカフェスレート</a>
      </div>
      <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav">
          <li class="dropdown">
            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
              フェス <span class="caret"></span>
            </a>
            <ul class="dropdown-menu"><?= implode('', array_map(
              function (Fest $fest): string {
                return Html::tag('li', Html::a(
                  Html::encode(vsprintf('#%d: %s', [
                    $fest->id,
                    $fest->name,
                  ])),
                  ['fest/view', 'id' => $fest->id]
                ));
              },
              Fest::find()->orderBy(['id' => SORT_DESC])->all()
            )) ?></ul>
          </li>
          <li class="dropdown">
            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
              リンク <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
              <li>
                <a href="http://www.nintendo.co.jp/wiiu/agmj/">スプラトゥーン 公式サイト</a>
              </li>
              <li>
                <a href="https://twitter.com/splatoonjp">
                  <span class="fab fa-twitter"></span> スプラトゥーン 公式ツイッター
                </a>
              </li>
              <li class="divider"></li>
              <li>
                <a href="https://stat.ink/">stat.ink</a>
              </li>
            </ul>
          </li>
          <li class="dropdown">
            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
              タイムゾーン <span class="caret"></span>
            </a>
            <ul class="dropdown-menu" id="timezone-list">
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </div>
</nav>
