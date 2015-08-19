fest.ink
========

[![MIT License](https://img.shields.io/github/license/fetus-hina/fest.ink.svg)](https://github.com/fetus-hina/fest.ink/blob/master/LICENSE)
[![Dependency Status](https://www.versioneye.com/user/projects/55d469e7265ff60022000dc9/badge.svg?style=flat)](https://www.versioneye.com/user/projects/55d469e7265ff60022000dc9)
[![Dependency Status](https://www.versioneye.com/user/projects/55d469e9265ff6001a000e50/badge.svg?style=flat)](https://www.versioneye.com/user/projects/55d469e9265ff6001a000e50)

https://fest.ink/ のソースコードです。

動作環境
--------

* PHP 5.4+
* SQLite3
* Node.js (`npm`)
* [webify](https://github.com/ananthakumaran/webify)

https://fest.ink/ は現在次の構成で動作しています。

* CentOS 7.1.1503 (x86_64)
* Nginx 1.9.x (mainline)
* SQLite 3.7.17 (標準)
* Node.js 0.10.36 ([EPEL](https://fedoraproject.org/wiki/EPEL))
* [SCL](https://www.softwarecollections.org/)
    - [rh-php56](https://www.softwarecollections.org/en/scls/rhscl/rh-php56/)
        - PHP 5.6.x
        - PHP-FPM

Apache+mod_php で動作させる場合は、 `runtime` ディレクトリと `db/fest.sqlite` ファイルの権限（所有者とパーミッション）に注意してください。

使い方
------

### PREREQUIREMENTS ###

イカモドキのウェブフォント生成のために `webify` コマンドが必要です。

[webifyのリリース](https://github.com/ananthakumaran/webify/releases)からコンパイル済みバイナリを取得するか、
`cabal install webify` で webify をインストールしてください。

CentOS 7 で EPEL が有効なら、こんな感じでインストールできるみたいです。

```sh
sudo yum install cabal-install
cabal update
cabal install webify
```

`webify` の実行ファイルへパスを通すことを忘れずに。(`~/.cabal/bin` あたり)

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

`Makefile` を見て頑張ってください



ライセンス
----------

see `LICENSE` file.

Copyright (C) 2015 AIZAWA Hina.

各モジュールの著作権は著作権者に帰属します。
