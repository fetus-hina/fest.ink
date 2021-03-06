fest.ink API
============

fest.inkと同等のサイトを構築できるであろうAPIを公開しています。

ご自由にお使いいただけますが、次の点にご留意ください。

* APIの仕様は突然変更になる（互換性が失われる）可能性があります。フェス中には変更しません。
* APIはfest.ink用に作成/使用しているものをそのまま流用しています。
    - このAPIの目的はあくまでfest.ink用が第一です。負荷状況等に応じて遮断する可能性があります。
    - このAPIにクライアントのブラウザから直接アクセスするのは避けてください。
        - あなたのサービスのサーバから一旦フェッチして、しばらくキャッシュしてください。データはそれほど頻繁には更新されません（5分に1回で充分でしょう）。
* 単にTwitterに勝率をながすだけなら、[@ikafest](https://twitter.com/ikafest) があります。

日時を表す型はJSONに存在しないため、次の二つの形式で格納して表します。

* 数値型表現

    UNIX時間で、小数点以下がある場合もあります。1の桁が秒単位になります(値が1増えると1秒後)。
    ほとんどのプログラミング言語/環境ではUNIX時間を取り扱えるはずですので、この形式が扱いやすいと思います。
    小数点以下の情報が不要な場合は単に切り捨ててください。
    JavaScript で取り扱う場合は 1000 倍してください（例: `new Date(at * 1000)`）。
    なお、閏秒は考慮しないタイプのUNIX時間です。

* 文字列型表現

    ISO 8601 形式で表現したものです。秒未満の精度が含まれる場合があります。秒未満の精度が含まれる場合、小数点はドットです（ISO 8601 ではカンマが標準ですが、ドットで表します）。
    タイムゾーンはデフォルトでは日本時間（`Asia/Tokyo`）になっていますが、クエリパラメータで変更できます。
    数値型の方が取り扱いやすいと思いますので主に人間が確認するためのデータだと思っていただいて構いません。
    なお、概ねμ秒精度で出力されているように見えますが、実際の精度は謎です。実際問題として利用する意味もありません。

文字列の電文上の表現はJSONで許容された全ての表現になり得ます。
つまり、非ASCII文字その他ほとんど全ての文字はエスケープを用いた表現(e.g. `\\` `\/` `\u3042`)になり得ます。
「なんちゃってパーサ」ではなく、きちんとRFC 7159(あるいはRFC 4627)に対応したパーサを通してください。

APIインタフェースはgzip圧縮したレスポンスを返すことができます。
リクエストヘッダに `Accept-Encoding: gzip` をつけてリクエストしてください。

----

## GET /index.json ##

[`GET https://fest.ink/index.json`](https://fest.ink/index.json)

フェスに関する情報と提供元サイトに関する情報を返します。

```js
{
  // 取得日時（サーバ側時刻）、UNIX時間で秒単位、小数点以下あり。
  "now":1442147020.9321,
  
  // "now" を ISO 8601 で表現したもの。小数点以下あり。小数点はドット。時間帯はパラメータ参照。
  "now_s":"2015-09-13T21:23:40.932145+09:00",
  
  // このJSONを提供したシステムの情報。特に表示義務等はありません。ただの参考情報です。
  "source":{
    "name":"イカフェスレート",
    "url":"https://fest.ink/",
    "version":"2.0.0-dev",
    "revision":["9a7e5668ed4a92c84725484949471f7a4708240e","9a7e566"] // Gitリビジョンのフルと短縮表記
  },

  // フェスの一覧
  "fests":[
    {
      // id は第 "n" 回と一致。/:id.json の呼び出しにも使います。
      "id":5,

      // フェス名称
      "name":"ボケ vs ツッコミ",

      // 開催期間
      "term":{
        // 開始・終了日時を UNIX 時間で秒単位。
        "begin":1442026800,
        "end":1442113200,

        // "begin"/"end" を ISO 8601 で表現したもの。時間帯はパラメータ参照。
        "begin_s":"2015-09-12T12:00:00+09:00",
        "end_s":"2015-09-13T12:00:00+09:00",

        // 呼び出し時点のフェスの状態
        //   "scheduled"  : 開催前
        //   "in session" : 開催中
        //   "closed"     : 開催終了
        "status":"closed",

        // 開催中なら true （"status" == "in session")
        "in_session":false
      },

      // 両陣営の情報
      "teams":{
        // アルファチーム = アオリ側チーム
        "alpha":{
          // チーム名
          "name":"ボケ",

          // インク色。RRGGBB。大文字小文字は期待しないでください。適当に設定したもので null の場合もあります。
          "ink":"d9612b"
        },

        // ブラボーチーム = ホタル側チーム
        "bravo":{
          "name":"ツッコミ",
          "ink":"5c7cb8"
        }
      }
    },
    // ...
  ],
  // 公式発表の結果（未発表なら "result": null）
  "result": {
    // 得票率
    "vote": {
      "alpha": 43,
      "bravo": 57,

      // 最終結果を計算するための係数。得票率は事実上 1 固定
      "multiply": 1
    },

    // 勝率
    "win": {
      "alpha": 49,
      "bravo": 51,

      // 最終結果を計算するための係数。2 だったり 4 だったり
      "multiply": 4
    }
  }
}
```

要素の出現順は一致しない可能性があります。また、フェスは開催順でない場合があり得るものと想定してください。（現在の実装上は新しいものから並びます）

### クエリパラメータ ###

* `tz` (e.g.: `https://fest.ink/index.json?tz=Europe%2fLondon`)

    日時の文字列表記のタイムゾーンを指定します。

    - デフォルトは `Asia/Tokyo` です
    - UTC(GMT) に設定する場合は `Etc/UTC` です
    - ほとんどの一般的な `地域/都市名` 表記が利用できます。具体的なリストは `/timezone.json` で取得できます。

----

## GET /:id.json ##

[`GET https://fest.ink/:id.json`](https://fest.ink/4.json)

指定されたフェスの情報を返します。サンプリングされた勝ち数も含まれます。（fest.inkのフェスページに表示される内容はこのJSONから全て求められます）

```js
{
  // index.json と内容が同じものは省略します
  "now":1442147721.5703,
  "now_s":"2015-09-13T21:35:21.570301+09:00",
  "id":5,
  "name":"ボケ vs ツッコミ",
  "term":{
    "begin":1442026800,
    "end":1442113200,
    "begin_s":"2015-09-12T12:00:00+09:00",
    "end_s":"2015-09-13T12:00:00+09:00",
    "in_session":false,
    "status":"closed"
  },
  "teams":{
    "alpha":{
      "name":"ボケ",
      "ink":"d9612b"
    },
    "bravo":{
      "name":"ツッコミ",
      "ink":"5c7cb8"
    }
  },

  // 各タイミングでの各チームのサンプリング済みの勝利数の配列
  "wins":[
    {
      // サンプリング日時（厳密にはこの少し前の任天堂次第のタイミング）
      "at":1442027042,
      "at_s":"2015-09-12T12:04:02+09:00",

      // 各陣営の勝利数
      // 概ね合計が90前後になるように見えますが一定しません。
      // サンプリング数の根拠も何もかも任天堂次第で謎です。
      "alpha":48,
      "bravo":43,

      // 各陣営のMVP一覧
      // パラメータ mvp が設定された時のみ表示
      "alphaMvp": null,
      "bravoMvp": null
    },
    // ...
  ],
  // 公式発表の結果（未発表なら "result": null）
  "result": {
    "vote": {
      "alpha": 43,
      "bravo": 57,
      "multiply": 1
    },
    "win": {
      "alpha": 49,
      "bravo": 51,
      "multiply": 4
    }
  }
}
```

要素の出現順は一致しない可能性があります。また、`wins`配列の中身は順不同と想定してください。（時系列で何かしたければ`at`を使用して並び替えてください）

なお、fest.ink のフェスページで表示している推定勝率は、

```
ALPHA = TOTAL(wins.alpha) / { TOTAL(wins.alpha) + TOTAL(wins.bravo) }
BRAVO = TOTAL(wins.bravo) / { TOTAL(wins.alpha) + TOTAL(wins.bravo) }
```

です。

### クエリパラメータ ###

* `tz` (e.g.: `https://fest.ink/5.json?tz=Europe%2fLondon`)

    日時の文字列表記のタイムゾーンを指定します。

    - デフォルトは `Asia/Tokyo` です
    - UTC(GMT) に設定する場合は `Etc/UTC` です
    - ほとんどの一般的な `地域/都市名` 表記が利用できます。具体的なリストは `/timezone.json` で取得できます。

* `mvp` (e.g.: `https://fest.ink/5.json?mvp=1`)

    各陣営のそのときのMVPをレスポンスに含むように変更します。

    - 値は `1` `t` `true` `y` `yes` のいずれかでオンに、それ以外でオフになります。
    - 転送量が大きい割に使い道がないので特別な事情が無い限りはオフにしておくことをおすすめします。
    - オンにしたときの JSON はつぎのようになります。
        
        ```js
        {
          // ...
          "wins":[
            {
              // ...
              "alphaMvp": [
                // プレーヤ名の配列
                "ポテトチップス",
                "イカ",
                "ゆゆゆ",
                "ざいこ",
                "チキン",
                // ...
              ],
              "bravoMvp": [
               // ...
              ]
            },
            // ...
          ],
          // ...
        }
        ```

----

## GET /flash.json ##

[`GET https://fest.ink/flash.json`](https://fest.ink/flash.json)

公式サイトの `recent_results.json` をエミュレートしたものを返します。

データはプル操作で取得している関係上、公式のデータから少し遅れたり漏れたりする可能性があります。

フェスを開催していないタイミングなど、表示するデータが無いときは空の配列になります。

```js
[]
```

フェスを開催しているタイミングなどの場合、MVP の名前とチームの配列が返されます。

このデータの内容は `/:id.json` に `mvp` をつけた時と同じで、集計すれば（サンプリングされた）勝利数とも一致します。

```js
[
  {
    "win_team_name": "ツッコミ",
    "win_team_mvp": "VPファイター"
  },
  {
    "win_team_name": "ツッコミ",
    "win_team_mvp": "がきこ"
  },
  {
    "win_team_name": "ツッコミ",
    "win_team_mvp": "ケンさん"
  },
  {
    "win_team_name": "ボケ",
    "win_team_mvp": "ごまだんご"
  },
  // ...
]
```

### クエリパラメータ ###

* `t` (e.g.: `https://fest.ink/flash.json?t=1442080720`)

    指定した時間のデータを取得します。
    値はUNIX時間で、 `t=1442080720` の場合 `2015-09-13T02:58:40+09:00` です。

* `extend` (e.g.: `https://fest.ink/flash.json?extend=1`)

    レスポンスにフェス情報を含みます。
    JSON の構造が次のように大きく変わります。

    ```js
    {
      // フェスの情報です。 index.json と同じ構造になります。
      // フェスが開催されていない時は null になります。
      "fest": {
        "id": 5,
        "name": "ボケ vs ツッコミ",
        "term": {
          "begin": 1442026800,
          "end": 1442113200,
          "begin_s": "2015-09-12T12:00:00+09:00",
          "end_s": "2015-09-13T12:00:00+09:00",
          "in_session": false,
          "status": "closed"
        },
        "teams": {
          "alpha": {
            "name": "ボケ",
            "ink": "d9612b"
          },
          "bravo": {
            "name": "ツッコミ",
            "ink": "5c7cb8"
          }
        },
        "result": {
          "vote": {
            "alpha": 43,
            "bravo": 57,
            "multiply": 1
          },
          "win": {
            "alpha": 49,
            "bravo": 51,
            "multiply": 4
          }
        }
      },

      // MVPの一覧はここに移されます
      "mvp": [
        {
          "win_team_name": "ボケ",
          "win_team_mvp": "クライ"
        },
        // ...
      ]
    }
    ```

* `tz` (e.g.: `https://fest.ink/flash.json?extend=1&tz=Europe%2fLondon`)

    日時の文字列表記のタイムゾーンを指定します。
    `extend` パラメータと同時に指定します。（`extend` を使わない場合は指定しても無意味です）

    - デフォルトは `Asia/Tokyo` です
    - UTC(GMT) に設定する場合は `Etc/UTC` です
    - ほとんどの一般的な `地域/都市名` 表記が利用できます。具体的なリストは `/timezone.json` で取得できます。

----

## GET /timezone.json ##

[`GET https://fest.ink/timezone.json`](https://fest.ink/timezone.json)

各 API に指定することができるタイムゾーン設定のリストです。

省略しているタイムゾーンがあります。
`東京`等、ユーザフレンドリーな都市名表記を行いたい場合は自前で頑張ってください。

```js
[
    // ...
    {
        // クエリパラメータ "tz" はこの識別子を渡します。地域/都市名になっています。
        "id":"Asia/Tokyo",
        
        // UTC からのオフセットが示されます。表示基準時間は「今」です。
        // 夏時間等の関係で常に同じ値になるとは限りません。
        "offset":"+09:00",
        
        // 位置情報します。
        // 中身は関知せずに取得できたものをそのまま吐き出します。
        // see: http://php.net/manual/ja/datetimezone.getlocation.php
        "location":
            // おそらく ISO 3166-1 alpha-2 の国コード
            "country_code":"JP",

            // どこか適当な地点の緯度経度
            // この例では東京都港区芝公園・赤羽橋駅あたりを指すようです。
            "latitude":35.65444,
            "longitude":139.74472,

            // 謎のコメント
            "comments":""
        }
    },
    // ...
]
```

----

[![CC-BY 4.0](https://i.creativecommons.org/l/by/4.0/88x31.png)](http://creativecommons.org/licenses/by/4.0/deed.ja)  
このドキュメントは、[クリエイティブ・コモンズ 表示 4.0 国際](http://creativecommons.org/licenses/by/4.0/deed.ja)ライセンスの下に提供されています。  
This document is licensed under a [Creative Commons Attribution 4.0 International License](http://creativecommons.org/licenses/by/4.0/deed.en).
