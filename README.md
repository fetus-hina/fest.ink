fest.ink
========

[![Yii2](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](http://www.yiiframework.com/)
[![MIT License](https://img.shields.io/github/license/fetus-hina/fest.ink.svg)](https://github.com/fetus-hina/fest.ink/blob/master/LICENSE)
[![Dependency Status](https://www.versioneye.com/user/projects/55d469e7265ff60022000dc9/badge.svg?style=flat)](https://www.versioneye.com/user/projects/55d469e7265ff60022000dc9)
[![Dependency Status](https://www.versioneye.com/user/projects/55d469e9265ff6001a000e50/badge.svg?style=flat)](https://www.versioneye.com/user/projects/55d469e9265ff6001a000e50)

https://fest.ink/ のソースコードです。

動作環境
--------

* PHP 7.0+
* SQLite3
* Node.js (`npm`)

https://fest.ink/ は現在次の構成で動作しています。

* CentOS 7.2.1511 (x86_64)
* Nginx 1.9.x (mainline)
* SQLite 3.7.17 (標準)
* [SCL](https://www.softwarecollections.org/)
    - [v8314](https://www.softwarecollections.org/en/scls/rhscl/v8314/)
        - V8 3.14.* (Used by Node.js)
    - [nodejs010](https://www.softwarecollections.org/en/scls/rhscl/nodejs010/)
        - Node.js 0.10.*
        - `nodejs010-nodejs`
        - `nodejs010-npm`
    * [Remi's RPM repository](http://rpms.famillecollet.com/)
        - `remi-safe` repository, it uses SCL mechanism
            - PHP 7.0.*
                - `php70-php-cli`
                - `php70-php-fpm`
                - `php70-php-gd`
                - `php70-php-mbstring`
                - `php70-php-mcrypt`
                - `php70-php-pdo`

Apache+mod_php で動作させる場合は、 `runtime` ディレクトリと `db/fest.sqlite` ファイルの権限（所有者とパーミッション）に注意してください。

CentOS 7 の標準 PHP は 5.4.16 です。このバージョンでは動作しません。

環境の作り方は `Dockerfile` を見るのが手っ取り早いです。素の CentOS 7 から環境を構築する手順はそこに書かれています。


使い方
------

### SETUP ###

1. `git clone` します

    ```sh
    git clone https://github.com/fetus-hina/fest.ink.git fest.ink
    cd fest.ink
    ```

2. `make` します

    ```sh
    make
    ```

3. ウェブサーバとかを良い感じにセットアップするときっと動きます。

### FAVICON ###

fest.ink の favicon はフリーライセンスではありません。
利用許可を得ている場合は次のように生成できます。

1. ライセンスキーを受け取ります

2. `config/favicon.license.txt` を作成し、ライセンスキーだけをその中に記載し保存します

3. `make` あるいは `make favicon` します

    ```sh
    make
    ```

### FETCH DATA ###

任天堂から新しいデータを取得するには、定期的に `/path/to/yii official-data/update` を実行します。フェスが開催されていないときは何もしません。


### TWITTER ###

Twitter 連携機能を有効にするには次のように設定します。

1. 必要であれば新規 Twitter アカウントを取得します。
2. 取得したアカウント、または、あなたのアカウントで新しいアプリを申請し、 `consumer key` と `consumer secret` を取得します。
3. `config/twitter.php` を開き、`consumerKey` と `consumerSecret` にそれぞれ取得した値を設定します。 `userToken` と `userSecret` はこの時点では空にしておきます。
4. コマンドラインで認証を行います。

    ```sh
    ./yii twitter/auth
    ```

5. 表示される指示に従って URL にアクセスし、取得したアカウントで認証します。認証すると PIN コードが表示されますのでコマンドラインにそのまま打ち込みます。
6. PIN コードの確認が行われた後、 `userToken` と `userSecret` に設定するべき値が表示されますので、 `config/twitter.php` に設定します。
7. データを収集したあと次のように実行すればツイートされます。実際には `cron` 等を設定することになります。ツイート内容は現在固定です。 `commands/TwitterController.php` を開いて該当箇所を確認してください。

    ```sh
    ./yii twitter/update
    ```


### DOCKER ###


テスト環境構築用の `Dockerfile` が同梱されています。自分でビルドするか、Docker Hub の [`jp3cki/festink`](https://hub.docker.com/r/jp3cki/festink/) でビルド済みのイメージが取得できます。

主要なソフトウェアのバージョンが合わせてあるため、本番環境とほぼ同じ環境ができあがるはずです。

現在の作業ディレクトリの中身が `/home/festink/fest.ink` にデプロイされます。その際 `vendor` などは一度消され、再構成されます。

コンテナを起動すると 80/TCP で Nginx が待ち受けています。ここへ接続して使用します。必要であれば `docker run` する時に `-p 8080:80` のように任意のポートにマップしてください。

なお、任天堂からのデータ取得の定期実行(cron)は意図的に組み込んでいません。


API
---

fest.ink からデータを取得する API は次のページを参照してください。
[https://fest.ink/api](https://fest.ink/api)


License (Source Codes)
----------------------

The MIT License (MIT)

Copyright (c) 2015-2016 AIZAWA Hina \<hina@bouhime.com\>

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

(Japanese)  
[参考和訳はこちらを参照してください](https://osdn.jp/projects/opensource/wiki/licenses%2FMIT_license)。  
※英語でのライセンス文章を正文とし、あくまで和訳は参考訳であるものとします。


License (Illustration)
----------------------

Copyright (C) 2015 AIZAWA Hina \<hina@bouhime.com\>  
Copyright (C) 2015 Chomado

The artwork of Inkling-Girl is NON-FREE License.  
Please contact us if you want to get a license.


(Japanese)  
fest.ink で使用しているイカガールのイラストはフリーなライセンスでは提供していません。  
何らかの事情でライセンスをご希望の方はお問い合わせください。


License (Documentation)
-----------------------

Copyright (C) 2015-2016 AIZAWA Hina \<hina@bouhime.com\>

[![CC-BY 4.0](https://i.creativecommons.org/l/by/4.0/88x31.png)](http://creativecommons.org/licenses/by/4.0/deed.ja)  
Documents of the fest.ink are licensed under a [Creative Commons Attribution 4.0 International License](http://creativecommons.org/licenses/by/4.0/deed.en).

(Japanese)  
ドキュメントは、[クリエイティブ・コモンズ 表示 4.0 国際](http://creativecommons.org/licenses/by/4.0/deed.ja)ライセンスの下に提供されています。


License (Application Template)
------------------------------

This software is including the Yii Framework 2 Basic Application Template.  
Its source codes is licensed to us under the (3-clause) BSD License below:

> The Yii framework is free software. It is released under the terms of
> the following BSD License.
> 
> Copyright © 2008 by Yii Software LLC (http://www.yiisoft.com)
> All rights reserved.
> 
> Redistribution and use in source and binary forms, with or without
> modification, are permitted provided that the following conditions
> are met:
> 
>  * Redistributions of source code must retain the above copyright
>    notice, this list of conditions and the following disclaimer.
>  * Redistributions in binary form must reproduce the above copyright
>    notice, this list of conditions and the following disclaimer in
>    the documentation and/or other materials provided with the
>    distribution.
>  * Neither the name of Yii Software LLC nor the names of its
>    contributors may be used to endorse or promote products derived
>    from this software without specific prior written permission.
> 
> THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
> "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
> LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
> FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
> COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
> INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
> BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
> LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
> CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
> LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
> ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
> POSSIBILITY OF SUCH DAMAGE.



