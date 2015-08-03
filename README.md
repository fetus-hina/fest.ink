fest.ink
========

https://fest.ink/ のソースコードです。

動作環境
--------

* PHP 5.4+
* SQLite3

https://fest.ink/ は現在次の構成で動作しています。

* CentOS 7.1.1503 (x86_64)
* Nginx 1.9.x (mainline)
* SQLite 3.7.17 (標準)
* [SCL](https://www.softwarecollections.org/)
    - [rh-php56](https://www.softwarecollections.org/en/scls/rhscl/rh-php56/)
        - PHP 5.6.x
        - PHP-FPM

Apache+mod_php で動作させる場合は、 `runtime` ディレクトリと `db/fest.sqlite` ファイルの権限（所有者とパーミッション）に注意してください。

使い方
------

### EASY WAY ###

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


### MANUAL ###

1. `git clone` します

    ```sh
    git clone https://github.com/fetus-hina/fest.ink.git fest.ink
    cd fest.ink
    ```

2. [Composer](https://getcomposer.org/) を入手します。システムに Composer が既にある場合はそちらを使っても構いません。

    ```sh
    curl -sS https://getcomposer.org/installer | php
    ```

3. 依存モジュール（フレームワークを含む）をインストールします。

    ```sh
    ./composer.phar install
    ```

4. SQLite DB の準備をします。`db/fest.sqlite` ファイルはウェブサーバ経由の PHP プロセス「も」書き込めるような権限にしておいてください。

    ```sh
    ./yii migrate/up
    ```

5. タイムゾーンデータベースの準備をします。

    ```sh
    mkdir web/res/tz
    pushd web/res/tz
    wget ftp://ftp.iana.org/tz/tzdata-latest.tar.gz
    tar zxvf tzdata-latest.tar.gz
    rm tzdata-latest.tar.gz
    popd
    ```

6. ウェブサーバとかを良い感じにセットアップするときっと動きます。


JavaScript/CSS の更新
---------------------

`fest.js`, `fest.css` は minify 前のコードが `resources` 以下にあります。

`resources` 以下を変更した後、次のように実行すると `web` 以下に minify されたコードが配置されます。

```sh
./yii resource
```

（ `uglifyjs` と `cleancss` が PATH の通ったところにある必要があります）

本当は assets を使うべきなのでしょうがよくわかりません :-(


ライセンス
----------

see `LICENSE` file.

Copyright (C) 2015 AIZAWA Hina.

各モジュールの著作権は著作権者に帰属します。
