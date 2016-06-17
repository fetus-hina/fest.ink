{{strip}}

{{title}}{{$app->name|escape}} | フェス「{{$fest->name|escape}}」の推定勝率{{/title}}

<div class="container" data-fest="{{$fest->id|escape}}">
  <div id="social">
    <a class="twitter-share-button" data-text="フェス「{{$fest->name|escape}}」の推定勝率" data-url="{{url route="/fest/view" id=$fest->id}}" data-hashtags="Splatoon,Splatfest,スプラトゥーン" data-count="horizontal" data-via="ikafest" href="https://twitter.com/intent/tweet">Tweet</a>
    &#32;
    <a class="twitter-follow-button" data-show-count="false" href="https://twitter.com/ikafest">Follow @ikafest</a>
  </div>

  {{include '@app/views/layouts/ad.tpl'}}

  <div class="btn-toolbar" role="toolbar">
    <div class="btn-group" role="group">
      <button type="button" class="btn btn-default auto-tooltip" id="btn-update" title="表示データを今すぐ更新します">
        <span class="glyphicon glyphicon-refresh"></span>
      </button>
    </div>
    <div class="btn-group" role="group">
      <button type="button" class="btn btn-default auto-tooltip" id="btn-autoupdate" title="自動更新のオンオフを切り替えます">
        <span class="glyphicon glyphicon-time"></span>
      </button>
      <div class="btn-group" role="group">
        <button type="button" class="btn btn-default dropdown-toggle auto-tooltip" id="btn-update-interval" title="自動更新間隔を設定します" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span class="caret"></span>
        </button>
        <ul class="dropdown-menu" id="dropdown-update-interval">
          {{if false}}
            <li><a href="javascript:;" class="update-interval" data-interval="5"><span class="glyphicon glyphicon-ok"></span> 5秒ごと</a></li>
            <li><a href="javascript:;" class="update-interval" data-interval="10"><span class="glyphicon glyphicon-ok"></span> 10秒ごと</a></li>
          {{/if}}
          <li><a href="javascript:;" class="update-interval" data-interval="120"><span class="glyphicon glyphicon-ok"></span> 2分ごと</a></li>
          <li><a href="javascript:;" class="update-interval" data-interval="300"><span class="glyphicon glyphicon-ok"></span> 5分ごと</a></li>
          <li><a href="javascript:;" class="update-interval" data-interval="600"><span class="glyphicon glyphicon-ok"></span> 10分ごと</a></li>
          <li><a href="javascript:;" class="update-interval" data-interval="900"><span class="glyphicon glyphicon-ok"></span> 15分ごと</a></li>
          <li><a href="javascript:;" class="update-interval" data-interval="1200"><span class="glyphicon glyphicon-ok"></span> 20分ごと</a></li>
          <li><a href="javascript:;" class="update-interval" data-interval="1800"><span class="glyphicon glyphicon-ok"></span> 30分ごと</a></li>
          <li><a href="javascript:;" class="update-interval" data-interval="3600"><span class="glyphicon glyphicon-ok"></span> 60分ごと</a></li>
        </ul>
      </div>
    </div>
    <div class="btn-group" role="group">
      <button type="button" class="btn btn-default btn-graphtype auto-tooltip" title="両チームの勝率を上下に並べて表示します" data-type="stack">
        <span class="fa fa-area-chart"></span> 上下
      </button>
      <button type="button" class="btn btn-default btn-graphtype auto-tooltip" title="両チームの勝率を重ねて表示します" data-type="overlay">
        <span class="fa fa-area-chart"></span> 重ね
      </button>
    </div>
    <div class="btn-group" role="group">
      <button type="button" class="btn btn-default auto-tooltip" id="btn-ink-color" title="インク色の使用有無を切り替えます">
        <span class="glyphicon glyphicon-tint"></span>
      </button>
      <button type="button" class="btn btn-default auto-tooltip" id="btn-scale" title="試合開催数を推定し、補正して表示します（実験的）">
        <span class="glyphicon glyphicon-adjust"></span>
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
    推定勝率: <span class="total-rate" data-team="alpha">取得中</span> VS <span class="total-rate" data-team="bravo">取得中</span> <span class="total-rate-info"></span>
  </h2>
  <p>
    {{$fest->alphaTeam->name|escape}}チーム: <span class="total-rate" data-team="alpha">取得中</span>（サンプル数：<span class="sample-count" data-team="alpha">???</span>）
  </p>
  <div class="progress">
    <div class="progress-bar progress-bar-danger progress-bar-striped total-progressbar" style="width:0%" data-team="alpha">
    </div>
  </div>
  <p>
    {{$fest->bravoTeam->name|escape}}チーム: <span class="total-rate" data-team="bravo">取得中</span>（サンプル数：<span class="sample-count" data-team="bravo">???</span>）
  </p>
  <div class="progress">
    <div class="progress-bar progress-bar-success progress-bar-striped total-progressbar" style="width:0%" data-team="bravo">
    </div>
  </div>

  <h2 id="graph-short">
    短期的勝率グラフ
  </h2>
  <p>
    その時点での直近の勝率をグラフにしたものです。「この時間帯はどっちが優勢」ということを示します。
  </p>
  <div class="rate-graph rate-graph-short">
  </div>

  <h2 id="graph-whole">
    長期的勝率グラフ
  </h2>
  <p>
    上部の「推定勝率」の遷移をグラフにしたものです。「最終的にどちらが勝ちそうか」ということを示します。
  </p>
  <p>
    <strong>背景が暗いところ</strong>（またはグラフが暗く描画されているところ）は両チームの勝率が50%:50%と仮定した時との<strong>有意差がない</strong>ことを示します。(p&lt;0.05)
  </p>
  <div class="rate-graph rate-graph-whole">
  </div>

  <h2 id="graph-whole2">
    誤差推定
  </h2>
  <p>
    「実際の勝率はこの範囲に大体入っているのではないかな」という範囲を示します。
  </p>
  <div class="rate-graph rate-graph-whole2">
  </div>

  <h2 id="graph-win">
    勝利数グラフ
  </h2>
  <p>
    数値はサンプリングされたものです。全数のどのくらいの割合で取得できているのかはわかりません。（サンプル数: <span class="sample-count">(取得中)</span>）
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
