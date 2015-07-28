{{strip}}
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>イカフェスレート</title>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="/res/gh-fork-ribbon.css">
    <link rel="stylesheet" href="/res/fest.css">
    <script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script src="/res/flot/jquery.flot.min.js"></script>
    <script src="/res/flot/jquery.flot.stack.min.js"></script>
    <script src="/res/flot/jquery.flot.time.min.js"></script>
    <script src="/res/fest.js" async></script>
    <script src="/res/twitter.js" async></script>
  </head>
  <body>
    {{include '@app/views/layouts/navbar.tpl'}}
    {{$content}}
    {{include '@app/views/layouts/forkme.tpl'}}
  </body>
</html>
{{/strip}}
