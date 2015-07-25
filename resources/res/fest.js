(function(window, undefined) {
    "use strict";
    var $ = window.jQuery;
    var NaN = Number.NaN;
    $(window.document).ready(function() {
        var festId = $('.container[data-fest]').attr('data-fest');
        if (!(festId + "").match(/^\d+$/)) {
            return;
        }

        var $totalRate = $('.total-rate');
        var $totalProgressBar = $('.total-progressbar');
        var $rateGraph = $('.rate-graph');
        var $lastUpdatedAt = $('.last-updated-at');
        var $lastFetchedAt = $('.last-fetched-at');
        if ($totalRate.length < 1 && $totalProgressBar.length < 1 && $rateGraph.length < 1 &&
                $lastUpdatedAt.length < 1 && $lastFetchedAt.length < 1) {
            return;
        }

        var update = function () {
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
                    'g': (totalCount > 0) ? totalGreen / totalCount : NaN
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
            $.getJSON(
                '/' + encodeURIComponent(festId) + '.json',
                { '_t': Math.floor(requestDate / 1000) },
                function (retJson) {
                    var total = calcCurrentTotal(retJson);
                    updateRateString(total);
                    updateRateProgressBar(total);
                    updateShortGraph(retJson);
                    updateWholeGraph(retJson);
                    updateTimestampString(requestDate, retJson);
                }
            );
        };

        window.setTimeout(function() {
                update.call(window);
                window.setInterval(function() {
                        update.call(window);
                    }, 10 * 60 * 1000
                );
            }, 1
        );
    });
})(window);
