<?php

declare(strict_types=1);

use app\assets\PixivBannerAsset;
use app\components\Version;
use app\components\widgets\IPVersionBadge;
use yii\helpers\Html;

$am = Yii::$app->assetManager;

$ver = Version::getVersion();
$revL = Version::getRevision();
$revS = Version::getShortRevision();

$renderIcon = function (array $data): string {
  return Html::a(
    strpos($data['icon'], '<') === false
      ? Html::tag('span', '', ['class' => $data['icon']])
      : $data['icon'],
    $data['url'],
    [
      'class' => 'auto-tooltip',
      'title' => $data['title'],
    ]
  );
};
?>
<footer class="footer">
  <div class="container text-muted">
    <div class="footer-version"><?= implode(', ', array_filter([
      vsprintf('%s Version %s', [
        Html::encode(Yii::$app->name),
        Html::a(
          Html::encode($ver),
          sprintf('https://github.com/fetus-hina/fest.ink/releases/tag/v%s', rawurlencode($ver))
        ),
      ]),
      ($revL && $revS)
        ? sprintf('Revision %s', Html::a(
          Html::encode($revS),
          sprintf('https://github.com/fetus-hina/fest.ink/commit/%s', rawurlencode($revL))
        ))
        : null,
    ])) ?></div>
    <div class="footer-author"><?= implode('<br>', [
      sprintf('Copyright &copy; 2015 AIZAWA Hina. %s', implode(' ', array_map($renderIcon, [
        [
          'icon' => 'fab fa-twitter',
          'title' => 'Twitter: fetus_hina',
          'url' => 'https://twitter.com/fetus_hina',
        ],
        [
          'icon' => 'fab fa-github',
          'title' => 'GitHub: fetus-hina',
          'url' => 'https://github.com/fetus-hina',
        ],
      ]))),
      sprintf('Illustrator: ちょまど. %s', implode(' ', array_map($renderIcon, [
        [
          'icon' => 'fab fa-twitter',
          'title' => 'Twitter: chomado',
          'url' => 'https://twitter.com/chomado',
        ],
        [
          'icon' => 'fab fa-github',
          'title' => 'GitHub: chomado',
          'url' => 'https://github.com/chomado',
        ],
        [
          'icon' => 'fas fa-blog',
          'title' => 'ちょまど帳',
          'url' => 'http://chomado.com/',
        ],
        [
          'icon' => 'fab fa-amazon',
          'title' => 'Amazon: 著者ページ',
          'url' => 'http://www.amazon.co.jp/%E3%81%A1%E3%82%87%E3%81%BE%E3%81%A9/e/B00WPPKOV8/?_encoding=UTF8&camp=247&creative=1211&linkCode=ur2&tag=fetusjp-22',
        ],
        [
          'icon' => Html::img(
            $am->getAssetUrl(
              PixivBannerAsset::register($this),
              'pixiv_logo.png'
            ),
            ['style' => [
              'height' => '1em',
              'width' => 'auto',
            ]]
          ),
          'title' => 'Pixiv: #6783972',
          'url' => 'http://www.pixiv.net/member.php?id=6783972',
        ],
      ]))),
    ]) ?></div>
    <div class="footer-nav"><?= implode(' | ', [
      Html::a('API', ['site/api']),
      Html::a('プライバシーポリシー', ['site/privacy']),
      Html::a('オープンソースライセンス', ['site/license']),
    ]) ?></div>
    <div class="footer-notice">
      このサイトは非公式(unofficial)サービスです。任天堂株式会社とは一切関係ありません。<br>
      このサイトの内容は無保証です。必ず公式情報をお確かめください。<br>
      このサイトのソースコードは<a href="https://github.com/fetus-hina/fest.ink">オープンソース(MIT License)です</a>。（※イラストを除く）<br>
      バグの報告・改善の提案などがありましたら、
        <a href="https://github.com/fetus-hina/fest.ink"><span class="fab fa-github"></span> GitHubのプロジェクト</a>に報告・提案するか、
        <a href="https://twitter.com/fetus_hina"><span class="fab fa-twitter"></span> @fetus_hina</a>にご連絡ください。<br>
      サイト内で表示している日時の時間帯は上部の「タイムゾーン」の設定に従っています。
      通常日本時間(<code>Asia/Tokyo</code>)で表示しています。
      現在の設定は<code><?= Html::encode(Yii::$app->getTimezone()) ?></code>です。<br>
    </div>
    <div class="footer-powered">
      <?= sprintf('Powered by %s', implode(', ', [
        Html::a(
          Html::encode('Yii Framework ' . Yii::getVersion()),
          'https://www.yiiframework.com/'
        ),
        Html::a(
          Html::encode('PHP ' . phpversion()),
          'https://www.php.net/'
        ),
      ])) ?><br>
      <?= IPVersionBadge::widget() . "\n" ?>
    </div>
  </div>
</footer>
