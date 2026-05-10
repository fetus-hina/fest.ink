<?php

declare(strict_types=1);

use yii\helpers\Html;

/** @var \yii\web\View $this */
/** @var string $message */

?>
<div class="container">
  <h1>
    エラー
  </h1>
  <p>
    <?= Html::encode($message) ?>
  </p>
</div>
