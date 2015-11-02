{{strip}}
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
            <ul class="dropdown-menu">
              {{$_allFest = \app\models\Fest::find()->orderBy("id DESC")->all()}}
              {{foreach $_allFest as $_fest}}
                <li>
                  <a href="{{path route="/fest/view" id=$_fest->id}}">
                    #{{$_fest->id|escape}}: {{$_fest->name|escape}}
                  </a>
                </li>
              {{/foreach}}
            </ul>
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
                  <span class="fa fa-twitter"></span> スプラトゥーン 公式ツイッター
                </a>
              </li>
              <li class="divider"></li>
              <li>
                <a href="https://splatoon.nintendo.net/">イカリング</a>
              </li>
              <li class="divider"></li>
              <li>
                <a href="https://stat.ink/">stat.ink</a>
              </li>
              <li>
                <a href="https://ikadenwa.ink/" class="auto-tooltip" title="フレンドマッチ中の通話に便利なサイトです">イカデンワ</a>
              </li>
              <li>
                <a href="http://ikazok.net/" class="auto-tooltip" title="チームの結成や管理、交流に便利なサイトです">イカナカマ</a>
              </li>
              <li>
                <a href="http://siome.ink/" class="auto-tooltip" title="TwitterアカウントとニンテンドーネットワークIDを登録して告知できるサイトです">siome</a>
              </li>
              <li>
                <a href="http://ika.akaihako.com/unislot" class="auto-tooltip" title="サザエガチャのシミュレータです">ウニスロット</a>
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
{{/strip}}
