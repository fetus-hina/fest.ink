fest.ink
========

[![Yii2](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](http://www.yiiframework.com/)
[![MIT License](https://img.shields.io/github/license/fetus-hina/fest.ink.svg)](https://github.com/fetus-hina/fest.ink/blob/master/LICENSE)

https://fest.ink/ のソースコードです。

動作環境
--------

* PHP 8.2+
* SQLite3
* Node.js (`node` `npm`)

https://fest.ink/ は現在次の構成で動作しています。

* RockyLinux 9 (x86_64)
* H2O
* SQLite 3.34.1
* EPEL
  - Brotli
  - Zopfli
  - pngcrush
* [Remi's RPM repository](http://rpms.remirepo.net/) with DNF modules
  - PHP 8.3
    - `php-cli`
    - `php-fpm`
    - `php-gd`
    - `php-mbstring`
    - `php-pdo`

Apache+mod_php で動作させる場合は、 `runtime` ディレクトリと `db/fest.sqlite` ファイルの権限（所有者とパーミッション）に注意してください。


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


API
---

fest.ink からデータを取得する API は次のページを参照してください。
[https://fest.ink/api](https://fest.ink/api)


License (Source Codes)
----------------------

The MIT License (MIT)

Copyright (c) 2015-2024 AIZAWA Hina \<hina@bouhime.com\>

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

Copyright (C) 2015-2024 AIZAWA Hina \<hina@bouhime.com\>

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



