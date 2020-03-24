<?php

declare(strict_types=1);

use app\assets\AppAsset;
use app\assets\FaviconAsset;
use yii\helpers\Html;

AppAsset::register($this);
FaviconAsset::register($this);

$metas = [
  'apple-mobile-web-app-capable' => 'yes',
  'format-detection' => 'telephone=no,email=no,address=no',
  'timezone' => Yii::$app->timeZone,
  'viewport' => 'width=device-width,initial-scale=1',
];
array_map(
  function (string $name, string $value): void {
    $this->registerMetaTag([
      'name' => $name,
      'content' => $value,
    ]);
  },
  array_keys($metas),
  array_values($metas)
);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode(
      $this->title ?: Yii::$app->name ?: 'イカフェスレート'
    ) ?></title>
    <?php $this->head(); echo "\n" ?>
  </head>
  <body>
    <?php $this->beginBody(); echo "\n" ?>
    <?= $this->render('navbar') . "\n" ?>
    <?= $content . "\n" ?>
    <?= $this->render('footer') . "\n" ?>
    <span id="event"></span>
    <?php $this->endBody(); echo "\n" ?>
  </body>
</html>
<?php $this->endPage() ?>
