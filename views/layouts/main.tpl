{{strip}}
{{\app\assets\AppAsset::register($this)|@void}}
{{$this->beginPage()|@void}}
  <!DOCTYPE html>
  <html lang="ja">
    <head>
      <meta charset="UTF-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="timezone" content="{{date_default_timezone_get()|escape}}">
      {{\yii\helpers\Html::csrfMetaTags()}}
      <title>イカフェスレート</title>
      {{$this->head()}}
    </head>
    <body>
      {{$this->beginBody()|@void}}
        {{include '@app/views/layouts/navbar.tpl'}}
        {{$content}}
      {{$this->endBody()|@void}}
    </body>
  </html>
{{$this->endPage()|@void}}
{{/strip}}
