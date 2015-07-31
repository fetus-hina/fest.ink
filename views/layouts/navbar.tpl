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
        <a class="navbar-brand ikamodoki" href="/">イカフェスレート</a>
      </div>
      <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav">
          <li class="dropdown">
            <a href="#" class="dropdown-toggle ikamodoki" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
              フェス <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
              {{$_allFest = \app\models\Fest::find()->orderBy("id DESC")->all()}}
              {{foreach $_allFest as $_fest}}
                <li>
                  <a href="{{url route="/fest/view" id=$_fest->id}}">
                    {{if $_fest->id === 1}}
                      <del class="auto-tooltip" title="データの取得を行っていないため何も表示されません">#{{$_fest->id|escape}}: {{$_fest->name|escape}}</del>
                    {{else}}
                      #{{$_fest->id|escape}}: {{$_fest->name|escape}}
                    {{/if}}
                  </a>
                </li>
              {{/foreach}}
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </div>
</nav>
{{/strip}}
