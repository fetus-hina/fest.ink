{strip}
<div class="container" data-fest="{{$fest->id|escape}}">
  <div class="starter-template">
    <h1>
      フェス「{{$fest->name|escape}}」の勝敗レート
    </h1>
    <p>
      スプラトゥーンの公式サイトで公開されているデータを基に推計したデータです。
      数パーセント程度の誤差を含んでいるものとして参考程度にどうぞ。
    </p>

    <div>
      <a class="twitter-share-button" data-text="フェス「{{$fest->name|escape}}」の勝敗レート" data-url="{{url route="/fest/view" id=$fest->id}}" data-hashtags="Splatoon" data-count="horizontal" href="https://twitter.com/intent/tweet">Tweet</a>
      <script>{literal}!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');{/literal}</script>
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

    {{include '@app/views/fest/attention.tpl'}}
  </div>
</div>
{/strip}
