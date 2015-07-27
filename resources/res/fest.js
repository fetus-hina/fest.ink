(function(window, undefined) {
    "use strict";
    var $ = window.jQuery;
    var NaN = Number.NaN;
    var defaultUpdateInterval = 10 * 60 * 1000;
    $(window.document).ready(function() {
        var festId = $('.container[data-fest]').attr('data-fest');
        if (!(festId + "").match(/^\d+$/)) {
            return;
        }

        var $updateButton = $('#btn-update');
        var $autoUpdateButton = $('#btn-autoupdate');
        var $updateIntervalMenu = $('#dropdown-update-interval');

        var $totalRate = $('.total-rate');
        var $sampleCount = $('.sample-count');
        var $totalProgressBar = $('.total-progressbar');
        var $rateGraph = $('.rate-graph');
        var $lastUpdatedAt = $('.last-updated-at');
        var $lastFetchedAt = $('.last-fetched-at');

        var localStorage = window.localStorage;
        var hasStorage = !!localStorage;

        // 自動更新設定
        var setAutoUpdate = function (enabled) { // {{{
            if (!hasStorage) {
                return;
            }
            localStorage.setItem("autoupdate", enabled ? "enabled" : "disabled");
        }; // }}}
        var getAutoUpdate = function () { // {{{
            if (hasStorage) {
                var value = localStorage.getItem("autoupdate");
                if (value === "disabled") {
                    return false;
                }
            }
            return true;
        }; // }}}

        // 自動更新用のタイマIDと現在の自動更新インターバル
        var autoUpdateTimerId = null;
        var autoUpdateInterval = null;

        // 自動更新インターバル
        var setUpdateInterval = function (interval) { // {{{
            if (!hasStorage) {
                return;
            }
            localStorage.setItem("update-interval", ~~interval);
        }; // }}}
        var getUpdateInterval = function () { // {{{
            if (hasStorage) {
                var interval = localStorage.getItem("update-interval");
                if ((interval + "").match(/^\d+$/)) {
                    return ~~interval;
                }
            }
            return defaultUpdateInterval;
        }; // }}}

        // fest.ink のサーバから最新情報を取ってきてページ内の情報を更新する
        var update = function () { // {{{
            var numberFormat = function(num) {
                // http://d.hatena.ne.jp/mtoyoshi/20090321/1237723345
                return num.toString().replace(/([\d]+?)(?=(?:\d{3})+$)/g, function(t){ return t + ','; });
            };
            var calcCurrentTotal = function (json) { // {{{
                var totalRed = 0;
                var totalGreen = 0;
                for (var i = 0; i < json.wins.length; ++i) {
                    totalRed += json.wins[i].r;
                    totalGreen += json.wins[i].g;
                }
                var totalCount = totalRed + totalGreen;
                return {
                    'r': (totalCount > 0) ? totalRed / totalCount : NaN,
                    'g': (totalCount > 0) ? totalGreen / totalCount : NaN,
                    'rSum': (totalCount > 0) ? totalRed : NaN,
                    'gSum': (totalCount > 0) ? totalGreen : NaN
                };
            }; // }}}
            var updateRateString = function (data) { // {{{
                $totalRate.each(function() {
                    var $this = $(this);
                    var rate = (function() {
                        switch($this.attr('data-team')) {
                            case 'red':     return data.r;
                            case 'green':   return data.g;
                            default:        return NaN;
                        }
                    })();
                    $this.text(
                        (rate === undefined || isNaN(rate))
                            ? '???'
                            : ((Math.round(rate * 1000) / 10) + "%")
                    );
                });
            }; // }}}
            var updateSampleCount = function (data) { // {{{
                $sampleCount.text(
                    (isNaN(data.rSum) || isNaN(data.gSum))
                        ? '???'
                        : numberFormat(data.rSum + data.gSum)
                );
            }; // }}}
            var updateRateProgressBar = function (data) { // {{{
                $totalProgressBar.each(function() {
                    var $this = $(this);
                    var rate = (function() {
                        switch($this.attr('data-team')) {
                            case 'red':     return data.r;
                            case 'green':   return data.g;
                            default:        return NaN;
                        }
                    })();
                    $this.width(
                        (rate === undefined || isNaN(rate))
                            ? '0%'
                            : ((rate * 100) + "%")
                    );
                });
            }; // }}}
            var getGraphOptions = function(term) { // {{{
                return {
                    series: {
                        stack: true,
                        lines: {
                            show: true,
                            fill: true,
                            steps: false
                        }
                    },
                    xaxis: {
                        mode: "time",
                        minTickSize: [30, "minute"],
                        timeformat: "%H:%M",
                        twelveHourClock: false,
                        timezone: 'browser',
                        min: term.begin * 1000,
                        max: term.end * 1000
                    },
                    yaxis: {
                        min: 0,
                        max: 100
                    },
                    colors: [
                        '#d9435f', '#5cb85c'
                    ]
                };
            }; // }}}
            var updateShortGraph = function (json) { // {{{
                var $targets = $rateGraph.filter('.rate-graph-short');
                if ($targets.length > 0) {
                    var red = [];
                    var green = [];
                    for (var i = 0; i < json.wins.length; ++i) {
                        var tmp = json.wins[i];
                        if (tmp.r + tmp.g > 0) {
                            red.push([
                                tmp.at * 1000,
                                tmp.r * 100 / (tmp.r + tmp.g)
                            ]);
                            green.push([
                                tmp.at * 1000,
                                tmp.g * 100 / (tmp.r + tmp.g)
                            ]);
                        }
                    }

                    $targets.each(function() {
                        var $area = $(this);
                        $area.empty();
                        $.plot($area, [red, green], getGraphOptions(json.term));
                    });
                }
            }; // }}}
            var updateWholeGraph = function (json) { // {{{
                var $targets = $rateGraph.filter('.rate-graph-whole');
                if ($targets.length > 0) {
                    var wins = json.wins.slice(0);
                    wins.sort(function(a, b) {
                        return a.at - b.at;
                    });

                    var redTotal = 0;
                    var greenTotal = 0;
                    var red = [];
                    var green = [];
                    for (var i = 0; i < wins.length; ++i) {
                        var tmp = wins[i];
                        redTotal += tmp.r;
                        greenTotal += tmp.g;
                        if (redTotal + greenTotal > 0) {
                            red.push([
                                tmp.at * 1000,
                                redTotal * 100 / (redTotal + greenTotal)
                            ]);
                            green.push([
                                tmp.at * 1000,
                                greenTotal * 100 / (redTotal + greenTotal)
                            ]);
                        }
                    }

                    $targets.each(function() {
                        var $area = $(this);
                        $area.empty();
                        $.plot($area, [red, green], getGraphOptions(json.term));
                    });
                }
            }; // }}}
            var updateTimestampString = function (requestDate, json) { // {{{
                var format = function (date) {
                    var zeroPadding = function (num) {
                        num = ~~num;
                        return (num > 9 ? '' : '0') + num;
                    };

                    return date.getFullYear() + '-' +
                        zeroPadding(date.getMonth() + 1) + '-' +
                        zeroPadding(date.getDate()) + ' ' +
                        zeroPadding(date.getHours()) + ':' +
                        zeroPadding(date.getMinutes());
                };

                // json.wins[n].at の最大値を取得
                var retJsonTimestamp = Math.max.apply(
                    null,
                    json.wins.map(function(value) {
                        return value.at;
                    })
                );

                $lastUpdatedAt.text(
                    retJsonTimestamp < 1 || retJsonTimestamp === undefined || isNaN(retJsonTimestamp)
                        ? '???'
                        : format(new Date(retJsonTimestamp * 1000))
                );

                $lastFetchedAt.text(
                    format(requestDate)
                );
            }; // }}}

            var requestDate = new Date();
            $updateButton.attr('disabled', 'disabled');
            $.getJSON(
                '/' + encodeURIComponent(festId) + '.json',
                { '_t': Math.floor(requestDate / 1000) },
                function (retJson) {
                    var total = calcCurrentTotal(retJson);
                    updateRateString(total);
                    updateRateProgressBar(total);
                    updateSampleCount(total);
                    updateShortGraph(retJson);
                    updateWholeGraph(retJson);
                    updateTimestampString(requestDate, retJson);
                    $updateButton.removeAttr('disabled');
                }
            );
        }; // }}}

        // 自動更新の有効化
        var enableAutoUpdate = function () { // {{{
            $autoUpdateButton.removeClass('btn-default').addClass('btn-primary');
            if (autoUpdateTimerId !== null) {
                window.clearInterval(autoUpdateTimerId);
            }
            autoUpdateInterval = getUpdateInterval();
            autoUpdateTimerId = window.setInterval(update, autoUpdateInterval);
            setAutoUpdate(true);
        }; // }}}

        // 自動更新の無効化
        var disableAutoUpdate = function () { // {{{
            $autoUpdateButton.addClass('btn-default').removeClass('btn-primary');
            if (autoUpdateTimerId !== null) {
                window.clearInterval(autoUpdateTimerId);
            }
            autoUpdateTimerId = null;
            autoUpdateInterval = null;
            setAutoUpdate(false);
        }; // }}}

        // 今すぐ更新するボタンが押されたときの処理
        $updateButton.click(update);

        // 自動更新ボタンが押された時の処理
        $autoUpdateButton.click(function() { // {{{
            if ($(this).hasClass('btn-primary')) {
                disableAutoUpdate();
            } else {
                enableAutoUpdate();
                update();
            }
        }); // }}}

        if (hasStorage) {
            // 自動更新間隔メニューが開いた時の処理
            // 適切なところにしるしをつける
            $updateIntervalMenu.parent().on('show.bs.dropdown', function() { // {{{
                var interval = (autoUpdateInterval === null) ? getUpdateInterval() : autoUpdateInterval;
                $('.update-interval', $updateIntervalMenu).each(function() {
                    var $a = $(this);
                    var $icon = $('.glyphicon', $a);
                    var targetInterval = (~~$a.attr('data-interval')) * 1000;
                    $icon.css('color', (targetInterval === interval) ? '#333' : 'rgba(0,0,0,0)');
                });
            }); // }}}

            // 自動更新間隔を変更するときの処理 
            $('.update-interval').click(function() { // {{{
                var targetInterval = (~~$(this).attr('data-interval')) * 1000;
                if (autoUpdateInterval === targetInterval) {
                    return;
                }
                disableAutoUpdate();
                setUpdateInterval(targetInterval);
                enableAutoUpdate();
                update();
            }); // }}}
        } else {
            $('#btn-update-interval').attr('disabled', 'disabled');
        }

        // 自動更新ボタンの状態を正しくする
        $autoUpdateButton.each(
            getAutoUpdate()
                ? function () { enableAutoUpdate(); }
                : function () { disableAutoUpdate(); }
        );

        window.setTimeout(function() {
                update.call(window);
            }, 1
        );
    });
})(window);
