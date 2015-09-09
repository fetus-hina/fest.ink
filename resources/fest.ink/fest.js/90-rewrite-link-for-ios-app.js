// Copyright (C) 2015 AIZAWA Hina | MIT License
// 
// apple-mobile-web-app-capable が yes の時、JavaScript で制御されたリンクを踏むと
// スタンドアロンモードが継続するらしいので、同一オリジンの時は location.href で移動するようにする
$(document).ready(function () {
    var navigator = window.navigator;
    if (!navigator || !navigator.standalone) {
        return;
    }

    document.addEventListener(
        'click',
        function (ev) {
            var target = ev.target;
            while (target.nodeName !== 'A' && target.nodeName !== 'HTML') {
                target = target.parentNode;
            }

            if (target.nodeName === 'A' &&
                    target.href &&
                    target.href.match(/^https?:/i) &&
                    target.href.indexOf(document.location.host) >= 0
            ) {
                ev.preventDefault();
                document.location.href = target.href;
            }
        },
        false
    );
});
