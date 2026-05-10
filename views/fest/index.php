<?php

declare(strict_types=1);

use yii\helpers\Html;
use yii\helpers\Url;

/** @var \yii\web\View $this */
/** @var \app\models\Fest[] $allFest */

?>
<div class="container">
  <h1>
    イカフェスレート
  </h1>
  <p>
    スプラトゥーンの公式サイトで公開されているデータを基にフェスの勝率を推定するサイトです。
  </p>
  <table class="table table-striped" id="fest-list">
    <tbody>
      <?php foreach ($allFest as $_fest) { ?>
        <tr>
          <td>
            <a href="<?= Html::encode(Url::to(['fest/view', 'id' => $_fest->id])) ?>" class="btn btn-primary">
              見る
            </a>
          </td>
          <td>
            第<?= Html::encode($_fest->id) ?>回
          </td>
          <td>
            <?= Html::encode($_fest->name) ?>
          </td>
          <td>
            <span class="fest-term-begin">
              <span class="fest-term-date"><?= Html::encode(date('Y-m-d', (int)$_fest->start_at)) ?></span>&#32;
              <span class="fest-term-time"><?= Html::encode(date('H:i T', (int)$_fest->start_at)) ?></span>
            </span> <span class="fest-term-range">～</span> <span class="fest-term-end">
              <span class="fest-term-date"><?= Html::encode(date('Y-m-d', (int)$_fest->end_at)) ?></span>&#32;
              <span class="fest-term-time"><?= Html::encode(date('H:i T', (int)$_fest->end_at)) ?></span>
            </span>
          </td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
  <?= $this->render('attention') ?>
</div>
