// Copyright (C) 2015 AIZAWA Hina | MIT License
(function (undefined) {
    window.fest = {
        getFestId: function () {
            return $('.container[data-fest]').attr('data-fest');
        },
        isFestPage: function () {
            return !!window.fest.getFestId();
        },
        numberFormat: function (number) {
            // http://d.hatena.ne.jp/mtoyoshi/20090321/1237723345
            return number.toString().replace(
                /([\d]+?)(?=(?:\d{3})+$)/g,
                function (t) {
                    return t + ',';
                }
            );
        },
        dateTimeFormat: function (date) {
            var zeroPadding = function (num) {
                num = ~~num;
                return (num > 9 ? '' : '0') + num;
            };
            return date.getFullYear() + '-' +
                zeroPadding(date.getMonth() + 1) + '-' +
                zeroPadding(date.getDate()) + ' ' +
                zeroPadding(date.getHours()) + ':' +
                zeroPadding(date.getMinutes()) + ' ' +
                date.getTimezoneAbbreviation();
        },
        getTimeBasedScaler: function () { // {{{
            return function (value/*, time*/) {
                return value;
            };
        }, // }}}
        getGraphOptions: function (term, teams) { // {{{
            var defaultInks = { alpha: 'd9435f', bravo: '5cb85c' };
            var useInkColor = window.fest.conf.useInkColor.get();
            return {
                series: {
                    stack: window.fest.conf.graphType.get() === "stack",
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
                    timezone: window.fest.conf.timezone.get(),
                    min: term.begin * 1000,
                    max: term.end * 1000
                },
                yaxis: {
                    min: 0,
                    max: 100
                },
                colors: [
                    '#' + (useInkColor ? teams.alpha.ink : defaultInks.alpha),
                    '#' + (useInkColor ? teams.bravo.ink : defaultInks.bravo)
                ]
            };
        }, // }}}
        isSignificant: function (alpha, bravo) {
            var expected = (alpha + bravo) / 2;
            if (expected == 0 || isNaN(expected)) {
                return undefined;
            }
            var a = Math.pow(alpha - expected, 2) / expected;
            var b = Math.pow(bravo - expected, 2) / expected;
            var chi2 = parseFloat((a + b).toFixed(5));
            if (chi2 >= 10.82757) {
                return 'p<.001';
            } else if (chi2 >= 6.63490) {
                return 'p<.01';
            } else if (chi2 >= 3.84146) {
                return 'p<.05';
            } else if (chi2 >= 2.70554) {
                return 'p<.10';
            } else {
                return 'n.s.';
            }
        },
        getSignificantRange: function (alpha, bravo) {
            var total = alpha + bravo;
            if (total == 0 || isNaN(total)) {
                return undefined;
            }
            var expected = total / 2;
            var lower = undefined;
            var upper = undefined;
            var permil;
            for (permil = 1; permil < 1000; ++permil) {
                var assumeAlpha = Math.round(total * permil / 1000);
                var assumeBravo = total - assumeAlpha;
                var sA = alpha + assumeAlpha;
                var sB = bravo + assumeBravo;
                var chi2 = (2 * total) * Math.pow(alpha * assumeBravo - bravo * assumeAlpha, 2) / (sA * sB * total * total);
                if (chi2 < 3.84146) {
                    if (lower === undefined) {
                        lower = permil;
                    }
                    upper = permil;
                } else if (upper !== undefined) {
                    break;
                }
            }
            return [ lower / 10, upper / 10 ];
        },
    };
})();
