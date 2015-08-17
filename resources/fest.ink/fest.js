(function(window, undefined) {
    "use strict";
    var $ = window.jQuery;
    var NaN = Number.NaN;
    var defaultUpdateInterval = 10 * 60 * 1000;
    var ourTimeZone = 'Asia/Tokyo';
    var defaultInks = { r: 'd9435f', g: '5cb85c' };
    $(window.document).ready(function() {
        // タイムゾーン設定 // {{{
        (function () {
            var tz = $('meta[name=timezone]').attr('content');
            if (tz) {
                ourTimeZone = tz;
            }
        })(); // }}}

        var date = function (millis) {
            return new timezoneJS.Date(millis, ourTimeZone);
        };

        var festId = $('.container[data-fest]').attr('data-fest');
        // フェスページのグラフやデータ {{{
        if ((festId + "").match(/^\d+$/)) {
            var $updateButton = $('#btn-update');
            var $autoUpdateButton = $('#btn-autoupdate');
            var $updateIntervalMenu = $('#dropdown-update-interval');
            var $graphTypeButton = $('.btn-graphtype');
            var $inkColorbutton = $('#btn-ink-color');

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

            // グラフ表示タイプ
            var setGraphType = function (type) { // {{{
                if (!hasStorage) {
                    return;
                }
                localStorage.setItem("graph-type", type);
            }; // }}}
            var getGraphType = function () { // {{{
                if (hasStorage) {
                    var type = localStorage.getItem("graph-type");
                    if (type === "overlay") {
                        return type;
                    }
                }
                return 'stack';
            }; // }}}

            // グラフ表示色
            var setUseInkColor = function (use) { // {{{
                if (!hasStorage) {
                    return;
                }
                localStorage.setItem("graph-ink", use ? "use" : "not use");
            }; // }}}
            var getUseInkColor = function () { // {{{
                if (hasStorage) {
                    var use = localStorage.getItem("graph-ink");
                    if (use === 'not use') {
                        return false;
                    }
                }
                return true;
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
                var updateRateProgressBar = function (data, inks) { // {{{
                    $totalProgressBar.each(function() {
                        var $this = $(this);
                        var rate = (function() {
                            switch($this.attr('data-team')) {
                                case 'red':     return data.r;
                                case 'green':   return data.g;
                                default:        return NaN;
                            }
                        })();
                        var color = (function() {
                            switch($this.attr('data-team')) {
                                case 'red':     return inks.r;
                                case 'green':   return inks.g;
                                default:        return null;
                            }
                        })();
                        $this.width(
                            (rate === undefined || isNaN(rate))
                                ? '0%'
                                : ((rate * 100) + "%")
                        );
                        $this.css(
                            'background-color',
                            (getUseInkColor() && color !== null)
                                ? ('#' + color)
                                : ''
                        );
                    });
                }; // }}}
                var getGraphOptions = function(term, inks) { // {{{
                    return {
                        series: {
                            stack: getGraphType() === "stack",
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
                            timezone: ourTimeZone,
                            min: term.begin * 1000,
                            max: term.end * 1000
                        },
                        yaxis: {
                            min: 0,
                            max: 100
                        },
                        colors: [
                            '#' + (getUseInkColor() ? inks.r : defaultInks.r),
                            '#' + (getUseInkColor() ? inks.g : defaultInks.g)
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
                            $.plot($area, [red, green], getGraphOptions(json.term, json.inks));
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
                            $.plot($area, [red, green], getGraphOptions(json.term, json.inks));
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
                            zeroPadding(date.getMinutes()) + " " +
                            date.getTimezoneAbbreviation();
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
                            : format(date(retJsonTimestamp * 1000))
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
                        updateRateProgressBar(total, retJson.inks);
                        updateSampleCount(total);
                        updateShortGraph(retJson);
                        updateWholeGraph(retJson);
                        updateTimestampString(date(requestDate.getTime()), retJson);
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

            // グラフの種類を変更する
            // UI の調整をするだけなので setGraphType してから呼ぶ
            var updateGraphType = function () { // {{{
                $graphTypeButton.addClass('btn-default').removeClass('btn-primary');
                $graphTypeButton
                    .filter("*[data-type=" + getGraphType() + "]")
                    .addClass('btn-primary')
                    .removeClass('btn-default');
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

                // グラフタイプ変更処理
                $graphTypeButton.click(function() { // {{{
                    var $this = $(this);
                    if ($this.hasClass('btn-primary')) {
                        return;
                    }
                    setGraphType($this.attr('data-type'));
                    updateGraphType();
                    update();
                }); // }}}

                // インク色を使うか設定を変更するボタンが押された時の処理
                $inkColorbutton.click(function() { // {{{
                    if ($(this).hasClass('btn-primary')) {
                        setUseInkColor(false);
                        $(this).removeClass('btn-primary').addClass('btn-default');
                    } else {
                        setUseInkColor(true);
                        $(this).removeClass('btn-default').addClass('btn-primary');
                    }
                    update();
                }); // }}}
            } else {
                $('#btn-update-interval').attr('disabled', 'disabled');
                $graphTypeButton.attr('disabled', 'disabled');
                $inkColorbutton.attr('disabled', 'disabled');
            }

            // 自動更新ボタンの状態を正しくする
            getAutoUpdate() ? enableAutoUpdate() : disableAutoUpdate();

            // グラフタイプボタンの状態を正しくする
            updateGraphType();

            // インク色を使うかどうかのボタンを正しくする
            $inkColorbutton
                .removeClass('btn-default')
                .addClass(getUseInkColor() ? 'btn-primary' : 'btn-default');

            // 初回更新開始
            window.setTimeout(function() { update(); }, 1);
        } // }}}

        // タイムゾーン選択
        (function () { // {{{
            var $timezoneMenu = $('#timezone-list');
            if ($timezoneMenu.length < 1) {
                return;
            }
            $timezoneMenu.parent().on('show.bs.dropdown', function () {
                var $li = $('li', $timezoneMenu);
                var onDisplay = function () {
                    var currentTimezone = $('meta[name=timezone]').attr('content');
                    $('.glyphicon-ok', $timezoneMenu).each(function() {
                        var $this = $(this);
                        $this.css(
                            'color',
                            $this.parent().attr('data-timezone') === currentTimezone
                                ? '#333'
                                : 'rgba(0,0,0,0)'
                        );
                    });
                };

                var changeTimezone = function (zone) {
                    if ($('meta[name=timezone]').attr('content') === zone) {
                        return;
                    }

                    var params = {'zone': zone};
                    params[$('meta[name=csrf-param]').attr('content')] = $('meta[name=csrf-token]').attr('content');

                    $.post(
                        '/timezone/set',
                        params,
                        function () {
                            window.location.reload();
                        }
                    );
                };

                if ($li.length > 0) {
                    onDisplay();
                    return;
                }

                // 読み込み開始
                $timezoneMenu.append(
                    $('<li>').css('text-align', 'center').append(
                        $('<span>').addClass('fa fa-spin fa-refresh')
                    )
                );

                $.getJSON(
                    '/timezone/list.json',
                    { '_t': Math.floor(new Date() / 1000) },
                    function (retJson) { // {{{
                        var zones = retJson.zones;
                        var currentArea = null;
                        var $currentArea = null;
                        var currentInitial = null;
                        var $currentInitial = null;
                        $timezoneMenu.empty().append(
                            $('<li>').append(
                                $('<a>', {'href': 'javascript:;', 'data-timezone': 'Asia/Tokyo'}).append(
                                    $('<span>').addClass('glyphicon glyphicon-ok')
                                ).append(
                                    ' 日本時間'
                                ).click(function () {
                                    changeTimezone('Asia/Tokyo')
                                })
                            )
                        ).append(
                            $('<li>').addClass('divider')
                        );
                        for (var i = 0; i < zones.length; ++i) {
                            var match = zones[i].zone.match(/^([^\/]+)\/((.).*)$/);
                            if (match) {
                                // "Asia"
                                if (currentArea !== match[1]) {
                                    currentArea = match[1];
                                    currentInitial = null;
                                    $currentInitial = null;
                                    $currentArea = $('<ul>').addClass('dropdown-menu');
                                    $timezoneMenu.append(
                                        $('<li>').addClass('dropdown-submenu').append(
                                            $('<a>', {'href': 'javascript:;', 'data-toggle': 'dropdown'}).text(
                                                currentArea
                                            )
                                        ).append(
                                            $currentArea
                                        )
                                    );
                                }

                                // Asia/"T"okyo
                                if (currentInitial !== match[3]) {
                                    currentInitial = match[3];
                                    $currentInitial = $('<ul>').addClass('dropdown-menu');
                                    $currentArea.append(
                                        $('<li>').addClass('dropdown-submenu').append(
                                            $('<a>', {'href': 'javascript:;', 'data-toggle': 'dropdown'}).text(
                                                currentInitial
                                            )
                                        ).append(
                                            $currentInitial
                                        )
                                    );
                                }


                                // "Asia"->"T"->"Asia/Tokyo"
                                $currentInitial.append(
                                    $('<li>').append(
                                        $('<a>', {'href': 'javascript:;', 'data-timezone': match[0]}).append(
                                            $('<span>').addClass('glyphicon glyphicon-ok')
                                        ).append(
                                            ' ' + match[0]
                                        ).click(function () {
                                            changeTimezone($(this).attr('data-timezone'));
                                        })
                                    )
                                );
                            }
                        }
                        onDisplay();
                    } // }}}
                );
            });
        })(); // }}}

        // initialize tooltip
        $('.auto-tooltip').tooltip({'container': 'body'});
    });
})(window);
