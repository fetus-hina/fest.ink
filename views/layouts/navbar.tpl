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
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
              フェス <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
              {{$_allFest = \app\models\Fest::find()->orderBy("id DESC")->all()}}
              {{foreach $_allFest as $_fest}}
                <li>
                  <a href="{{url route="/fest/view" id=$_fest->id}}">
                    #{{$_fest->id|escape}}: {{$_fest->name|escape}}
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
