fest.ink
========

https://fest.ink/ のソースコードです。

動作環境
--------

* PHP 5.4+
* SQLite3
* https://fest.ink/ は Nginx + FastCGI + PHP-FPM で動いていますが、権限に気をつければ `mod_php` でも普通に動くとは思います。

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
    cd db
	cat sqls/01/table.sql | sqlite3 fest.sqlite
	sqls/01/2ndfest.php   | sqlite3 fest.sqlite
	sqls/02/3rdfest.php   | sqlite3 fest.sqlite
    ```

5. ウェブサーバとかを良い感じにセットアップするときっと動きます。


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
