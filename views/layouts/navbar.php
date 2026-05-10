<?php

declare(strict_types=1);

use app\models\Fest;
use yii\helpers\Html;

?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
  <div class="container">
    <a class="navbar-brand" href="/">イカフェスレート</a>
    <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbar-content" aria-controls="navbar-content" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbar-content">
      <ul class="navbar-nav">
        <li class="nav-item dropdown">
          <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" role="button" aria-expanded="false">
            フェス
          </a>
          <ul class="dropdown-menu"><?= implode('', array_map(
            function (Fest $fest): string {
              return Html::tag('li', Html::a(
                Html::encode(vsprintf('#%d: %s', [
                  $fest->id,
                  $fest->name,
                ])),
                ['fest/view', 'id' => $fest->id],
                ['class' => 'dropdown-item']
              ));
            },
            Fest::find()->orderBy(['id' => SORT_DESC])->all()
          )) ?></ul>
        </li>
        <li class="nav-item dropdown">
          <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" role="button" aria-expanded="false">
            リンク
          </a>
          <ul class="dropdown-menu">
            <li>
              <a class="dropdown-item" href="http://www.nintendo.co.jp/wiiu/agmj/">スプラトゥーン 公式サイト</a>
            </li>
            <li>
              <a class="dropdown-item" href="https://twitter.com/splatoonjp">
                <span class="fab fa-twitter"></span> スプラトゥーン 公式ツイッター
              </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <a class="dropdown-item" href="https://stat.ink/">stat.ink</a>
            </li>
          </ul>
        </li>
        <li class="nav-item dropdown">
          <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" role="button" aria-expanded="false">
            タイムゾーン
          </a>
          <ul class="dropdown-menu" id="timezone-list">
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>
