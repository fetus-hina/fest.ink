// Copyright (C) 2016 AIZAWA Hina | MIT License
$(document).ready(function () {
    if (!window.fest.isFestPage()) {
        return;
    }
    var $template = $('#social .share-button');
    if ($template.length < 1) {
        return;
    }
    var NaN = Number.NaN;
    var $event = $('#event');
    $event.on('receiveUpdateData', function (ev, data_) {
        var date = data_.date;
        var json = data_.json;
        var summary = data_.summary;
        var $newButton = $template.clone()
                .css('display', 'inline')
                .addClass('twitter-share-button');
        if (!isNaN(summary.aRange.min)) {
            $newButton.attr(
                'data-text', 
                [
                    "フェス「" + json.name + "」の推定勝率",
                    (function(){
                        var chi2 = window.fest.isSignificant(summary.aSumRaw, summary.bSumRaw);
                        var teamName = (summary.aSumRaw > summary.bSumRaw)
                                ? json.teams.alpha.name
                                : json.teams.bravo.name;
                        switch (chi2) {
                            case 'n.s.':
                                return '【優劣不明】';

                            case 'p<.10':
                                return '【' + teamName + 'チーム優勢の気配】';

                            case 'p<.05':
                                return '【' + teamName + 'チーム優勢？】';

                            case 'p<.01':
                                return '【' + teamName + 'チーム優勢の模様】';

                            case 'p<.001':
                                return '【' + teamName + 'チーム優勢】';

                            default:
                                return '';
                        }
                    })(),
                    json.teams.alpha.name + ": " + summary.aRange.min.toFixed(1) + "～" + summary.aRange.max.toFixed(1) + "%",
                    json.teams.bravo.name + ": " + summary.bRange.min.toFixed(1) + "～" + summary.bRange.max.toFixed(1) + "%",
                ].join("\n") + "\n"
            );
        }
        if (window.twttr && window.twttr.widgets) {
            // 実体化されていないボタンになるべき要素を消す
            $('.twitter-share-button').remove();
            // 実体化済みのボタンを消す
            $('.twitter-share-button-rendered').remove();

            // 新しいボタン（になるべき要素）を追加する
            $newButton.insertAfter($template);
            window.twttr.widgets.load();
        }
    });
});
