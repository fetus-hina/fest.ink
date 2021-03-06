{{strip}}

{{title}}{{$app->name|escape}} | フェス「{{$fest->name|escape}}」の推定勝率{{/title}}

<div class="container" data-fest="{{$fest->id|escape}}">
  <div id="social">
    <a class="share-button" data-text="フェス「{{$fest->name|escape}}」の推定勝率" data-url="{{url route="/fest/view" id=$fest->id}}" data-via="ikafest" href="https://twitter.com/intent/tweet" style="display:none">Tweet</a>
    &#32;
    <a class="twitter-follow-button" data-show-count="false" href="https://twitter.com/ikafest">Follow @ikafest</a>
  </div>
  <div class="btn-toolbar" role="toolbar">
    <div class="btn-group" role="group">
      <button type="button" class="btn btn-default btn-graphtype auto-tooltip" title="両チームの勝率を上下に並べて表示します" data-type="stack">
        <span class="fas fa-chart-area"></span> 上下
      </button>
      <button type="button" class="btn btn-default btn-graphtype auto-tooltip" title="両チームの勝率を重ねて表示します" data-type="overlay">
        <span class="fas fa-chart-area"></span> 重ね
      </button>
    </div>
    <div class="btn-group" role="group">
      <button type="button" class="btn btn-default auto-tooltip" id="btn-ink-color" title="インク色の使用有無を切り替えます">
        <span class="fas fa-tint"></span>
      </button>
    </div>
  </div>

  {{registerCssFile url="https://fonts.googleapis.com/css?family=Chango"}}
  {{registerCss}}
    #official-result-container .result-number,
    #official-result-container .result-percent,
    #official-result-container .result-multiply{
      font-family:'Chango',cursive
    }
  {{/registerCss}}
  <div id="official-result">
    <h1>
      フェス「{{$fest->name|escape}}」の結果
    </h1>
    <div id="official-result-container">
    </div>
    <hr>
  </div>

  <h1>
    フェス「{{$fest->name|escape}}」の推定勝率
  </h1>
  <p>
    スプラトゥーンの公式サイトで公開されているデータを基に推計したデータです。
    統計的な理由による誤差推定も推定していますがそれも含めて必ずしも当たるとは限りません。
  </p>
  <p>
    統計学的な処理（誤差率や有意差等）は実験的な実装です。
  </p>
  <p>
    <a href="https://blog.fetus.jp/201606/371.html">何を表示しているかや誤差の意味に関する説明はこちら</a>をご覧ください。
  </p>
  {{$statink = $fest->statInkUrl}}
  {{if $statink}}
    <p>
      <a href="{{$statink|escape}}">
        stat.inkの投稿情報に基づいて計算したデータはこちらです。
      </a>
    </p>
  {{/if}}
  {{if $fest->is_multiple_region}}
    <p>
      <strong style="color:red">
        このフェスは複数の地域にまたがって開催されています。<br>
        統計対象はおそらく日本のみとなっていますので、他の地域を合わせてみると結果は大きくずれるかもしれません。
      </strong>
    </p>
  {{/if}}

  <h2 id="rate">
    推定勝率: <span class="total-rate-info"></span> <span class="total-rate" data-team="alpha">取得中</span> VS <span class="total-rate" data-team="bravo">取得中</span>
  </h2>
  <p>
    {{$fest->alphaTeam->name|escape}}チーム: <span class="total-rate" data-team="alpha">取得中</span>（サンプル数：<span class="sample-count" data-team="alpha">???</span>）
  </p>
  <div class="progress">
    <div class="progress-bar progress-bar-danger progress-bar-striped total-progressbar total-progressbar-certain" style="width:0%" data-team="alpha">
    </div>
    <div class="progress-bar progress-bar-danger progress-bar-striped total-progressbar total-progressbar-uncertain" style="width:0%" data-team="alpha">
    </div>
  </div>
  <p>
    {{$fest->bravoTeam->name|escape}}チーム: <span class="total-rate" data-team="bravo">取得中</span>（サンプル数：<span class="sample-count" data-team="bravo">???</span>）
  </p>
  <div class="progress">
    <div class="progress-bar progress-bar-success progress-bar-striped total-progressbar total-progressbar-certain" style="width:0%" data-team="bravo">
    </div>
    <div class="progress-bar progress-bar-success progress-bar-striped total-progressbar total-progressbar-uncertain" style="width:0%" data-team="bravo">
    </div>
  </div>

  <h2 id="graph-whole2">
    勝率推定グラフ
  </h2>
  <p>
    標本から真の勝率を推定した範囲の遷移を示します。
  </p>
  <p>
    背景が暗いところ</strong>は両チームの勝率が50%:50%と仮定した時との有意差がみられないことを示します。(p&lt;0.05)
  </p>
  <div class="rate-graph rate-graph-whole2">
  </div>

  <h2 id="graph-whole">
    勝率グラフ（標本・全期間）
  </h2>
  <p>
    取得した標本を単純に合計してそのまま勝率として表示したグラフです。
  </p>
  <p>
    背景が暗いところ</strong>は両チームの勝率が50%:50%と仮定した時との有意差がみられないことを示します。(p&lt;0.05)
  </p>
  <div class="rate-graph rate-graph-whole">
  </div>

  <h2 id="graph-short">
    勝率グラフ（標本・取得時のみ）
  </h2>
  <p>
    その時点で取得した標本の勝率をグラフにしたものです。
  </p>
  <div class="rate-graph rate-graph-short">
  </div>

  <h2 id="graph-win">
    標本数グラフ
  </h2>
  <p>
    標本として取得した各チームの勝利数の合計をグラフにしたものです。
    （標本数: <span class="sample-count" data-team="alpha">(取得中)</span> + <span class="sample-count" data-team="bravo">(取得中)</span> = <span class="sample-count">(取得中)</span>）
  </p>
  <div class="rate-graph rate-graph-win-count">
  </div>

  <h2 id="about-data">
    表示している情報
  </h2>
  <p>
    フェス開催期間: {{$fest->start_at|date_format:'%Y-%m-%d %H:%M %Z'|escape}} ～ {{$fest->end_at|date_format:'%Y-%m-%d %H:%M %Z'|escape}}
  </p>
  <p>
    <span title="サーバが任天堂から最後にデータを取得したタイミングです">データ最終更新: <span class="last-updated-at">(取得中)</span></span>、
    <span title="あなた（ブラウザ）が fest.ink のサーバから最後にデータを取得したタイミングです">データ最終取得: <span class="last-fetched-at">(取得中)</span></span>、
    サンプル数: <span class="sample-count">(取得中)</span>
  </p>

  {{include '@app/views/fest/attention.tpl'}}
</div>
{{/strip}}
