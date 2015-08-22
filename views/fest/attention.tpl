{{strip}}
  <h1 class="ikamodoki">ごちゅうい</h1>
  <ul>
    <li>
      このページはスプラトゥーン公式（任天堂）とは一切関係がありません。個人が勝手に推計しているものです。
    </li>
    <li>
      表示している日時は上部の「<span class="ikamodoki">タイムゾーン</span>」の設定に従っています。
      デフォルトは日本時間(JST, <code>Asia/Tokyo</code>)です。
      現在、<code>{{$app->getTimezone()|escape}}</code>で表示しています。
    </li>
    <li>
      表示しているデータは公式発表ではありません。
    </li>
    <li>
      使用しているデータは<a href="https://ja.wikipedia.org/wiki/%E6%A8%99%E6%9C%AC%E8%AA%BF%E6%9F%BB">サンプリング</a>されたものです。
      もととなるサンプリングが公平なものかはわかりません。また、開催されているであろう全試合数に対してかなり少ないデータとなっていますので誤差がかなりあるものと思われます。
    </li>
    <li>
      実際には夕方から夜にかけては多く、未明は少なく試合が開催されているものと思われますが、全体の試合数の推計ができないためやむを得ず「ずっと同じ試合数」と仮定した集計を行っています。
      そのため、誤差がかなりあるものと思われます。
    </li>
    <li>
      フェスページの表示は上部のボタンの設定に従って自動更新されます（フェスの開催期間にかかわらずずっと更新します）。
      デフォルトでは10分に一回自動更新されます。
      通信量は大したことありませんが気になる方はお気を付けください。
    </li>
    <li>
      ページの一部に<a href="http://aramugi.com/?page_id=807" class="ikamodoki">イカモドキ</a>フォントを利用しています。
    </li>
    <li>
      このサイトのソースはオープンソース (MIT License) です。
      <a href="https://github.com/fetus-hina/fest.ink" class="auto-tooltip" title="GitHubプロジェクトページ">
        <span class="fa fa-github"></span> GitHub で開発・公開しています
      </a>。
      <iframe src="https://ghbtns.com/github-btn.html?user=fetus-hina&amp;repo=fest.ink&amp;type=star&amp;count=true" frameborder="0" scrolling="0" width="100" height="20"></iframe>
      <iframe src="https://ghbtns.com/github-btn.html?user=fetus-hina&amp;repo=fest.ink&amp;type=fork&amp;count=true" frameborder="0" scrolling="0" width="100" height="20"></iframe>
    </li>
    <li>
      連絡先:&#32;
      <a href="https://github.com/fetus-hina" class="auto-tooltip" title="GitHub（バグ報告等はこちら）">
        <span class="fa fa-github"></span> fetus-hina
      </a>,&#32;
      <a href="https://twitter.com/fetus_hina" class="auto-tooltip" title="Twitter">
        <span class="fa fa-twitter"></span> fetus_hina
      </a>
    </li>
    <li>
      {{$_ver = \app\components\Version::getVersion()}}
      {{$_revL = \app\components\Version::getRevision()}}
      {{$_revS = \app\components\Version::getShortRevision()}}
      Version <a href="https://github.com/fetus-hina/fest.ink/releases/tag/v{{$_ver|escape:url|escape}}">{{$_ver|escape}}</a>
      {{if $_revL && $_revS}}
        , Revision <a href="https://github.com/fetus-hina/fest.ink/commit/{{$_revL|escape:url|escape}}">{{$_revS|escape}}</a>
      {{/if}}
    </li>
    <li>
      {{\app\assets\PoweredAsset::register($this)|@void}}
      {{$_am = $this->getAssetManager()}}
      {{$_imgUrlYii = $_am->getAssetUrl($_am->getBundle('app\assets\PoweredAsset', false), 'yii.svg')}}
      {{$_imgUrlPhp = $_am->getAssetUrl($_am->getBundle('app\assets\PoweredAsset', false), 'php-power-micro.png')}}
      <a href="http://www.yiiframework.com/" title="Powered by Yii Framework {{\Yii::getVersion()|escape}}">
        <img src="{{$_imgUrlYii|escape}}" alt="Powered by Yii" title="">
      </a>
      &#32;
      {{$_phpv = phpversion()}}{{* PHP コード開始タグと解釈される問題があるので一回変数に入れる *}}
      <a href="http://php.net/" title="Powered by PHP {{$_phpv|escape}}">
        <img src="{{$_imgUrlPhp|escape}}" alt="Powered by PHP" title=""> 
      </a>
    </li>
    <li>
      Copyright &copy; 2015 AIZAWA Hina.
    </li>
  </ul>
{{/strip}}
