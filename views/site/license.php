<?php

declare(strict_types=1);

use yii\helpers\Html;

/** @var \yii\web\View $this */
/** @var \stdClass $myself */
/** @var \stdClass[] $depends */

$this->title = 'イカフェスレート | ライセンス';

?>
<div class="container">
  <h1>
    ライセンス
  </h1>
  <div>
    <h2>
      <?= Html::encode($myself->name) ?>
    </h2>
    <div class="license-body">
      <?= $myself->html ?>
    </div>
  </div>
  <hr>
  <?php foreach ($depends as $software) { ?>
    <div>
      <h2>
        <?= Html::encode($software->name) ?>
      </h2>
      <div class="license-body">
        <?= $software->html ?>
      </div>
    </div>
  <?php } ?>
</div>
