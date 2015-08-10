{{strip}}
<div class="container" data-fest="{{$fest->id|escape}}">
  <div class="starter-template">
    <div class="btn-toolbar" role="toolbar">
      <div class="btn-group" role="group">
        <button type="button" class="btn btn-default auto-tooltip" id="btn-update" title="表示データを今すぐ更新します">
          <span class="glyphicon glyphicon-refresh"></span> 更新
        </button>
      </div>
      <div class="btn-group" role="group">
        <button type="button" class="btn btn-default auto-tooltip" id="btn-autoupdate" title="自動更新のオンオフを切り替えます">
          自動更新
        </button>
        <div class="btn-group" role="group">
          <button type="button" class="btn btn-default dropdown-toggle auto-tooltip" id="btn-update-interval" title="自動更新間隔を設定します" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="caret"></span>
          </button>
          <ul class="dropdown-menu" id="dropdown-update-interval">
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
          上下に並べて表示
        </button>
        <button type="button" class="btn btn-default btn-graphtype auto-tooltip" title="両チームの勝率を重ねて表示します" data-type="overlay">
          重ねて表示
        </button>
      </div>
    </div>

    <h1>
      フェス「{{$fest->name|escape}}」の勝敗レート
    </h1>
    <p>
      スプラトゥーンの公式サイトで公開されているデータを基に推計したデータです。
      ±2パーセントポイント程度の誤差を含んでいるものとして参考程度にどうぞ。
    </p>

    <div>
      <a class="twitter-share-button" data-text="フェス「{{$fest->name|escape}}」の勝敗レート" data-url="{{url route="/fest/view" id=$fest->id}}" data-hashtags="Splatoon" data-count="horizontal" href="https://twitter.com/intent/tweet">Tweet</a>
    </div>

    <h2>
      推定勝率: <span class="total-rate" data-team="red">(取得中)</span> vs <span class="total-rate" data-team="green">(取得中)</span>
    </h2>
    <p>
      {{$fest->redTeam->name|escape}}チーム: <span class="total-rate" data-team="red">(取得中)</span>
    </p>
    <div class="progress">
      <div class="progress-bar progress-bar-danger progress-bar-striped total-progressbar" style="width:0%" data-team="red">
      </div>
    </div>
    <p>
      {{$fest->greenTeam->name|escape}}チーム: <span class="total-rate" data-team="green">(取得中)</span>
    </p>
    <div class="progress">
      <div class="progress-bar progress-bar-success progress-bar-striped total-progressbar" style="width:0%" data-team="green">
      </div>
    </div>

    <h2>
      短期的勝率グラフ
    </h2>
    <p>
      その時点での直近の勝率をグラフにしたものです。「この時間帯はどっちが優勢」ということを示します。
    </p>
    <div class="rate-graph rate-graph-short">
    </div>

    <h2>
      長期的勝率グラフ
    </h2>
    <p>
      上部の「推定勝率」の遷移をグラフにしたものです。「最終的にどちらが勝ちそうか」ということを示します。
    </p>
    <div class="rate-graph rate-graph-whole">
    </div>

    <h2>
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
</div>
{{/strip}}
