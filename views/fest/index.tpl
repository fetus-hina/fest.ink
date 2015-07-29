{{strip}}
<div class="container">
  <div class="starter-template">
    <h1 class="ikamodoki">
      イカフェスレート
    </h1>
    <p>
      スプラトゥーンの公式サイトで公開されているデータを基にフェスの勝率を推定するサイトです。
    </p>
    <table class="table table-stripe">
      <tbody>
        {{foreach $allFest as $_fest}}
          <tr>
            <td style="width:6em">
              <a href="{{url route="/fest/view" id=$_fest->id}}" class="btn btn-primary">
                表示
              </a>
            </td>
            <td style="width:4em">
              第{{$_fest->id|escape}}回
            </td>
            <td>
              {{$_fest->name|escape}}
            </td>
            <td style="width:20em">
              {{$_fest->start_at|date_format:'%Y-%m-%d %H:%M'|escape}}
              &#32;～&#32;
              {{$_fest->end_at|date_format:'%Y-%m-%d %H:%M'|escape}}
            </td>
          </tr>
        {{/foreach}}
      </tbody>
    </table>
    <p>
      ※第1回分はページは作ってありますが集計していないので何も表示されません。
    </p>

    {{include '@app/views/fest/attention.tpl'}}
  </div>
</div>
{{/strip}}
