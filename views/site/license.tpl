{{strip}}
{{set layout="main.tpl"}}
{{set title="イカフェスレート | ライセンス"}}
<div class="container">
  <h1>
    ライセンス
  </h1>
  <div>
    <h2>
      {{$myself->name|escape}}
    </h2>
    <div class="license-body">
      {{$myself->html}}
    </div>
  </div>
  <hr>
  {{foreach $depends as $software}}
    <div>
      <h2>
        {{$software->name|escape}}
      </h2>
      <div class="license-body">
        {{$software->html}}
      </div>
    </div>
  {{/foreach}}
</div>
{{/strip}}
