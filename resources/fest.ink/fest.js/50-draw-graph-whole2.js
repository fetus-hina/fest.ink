// Copyright (C) 2015 AIZAWA Hina | MIT License
$(document).ready(function () {
    if (!window.fest.isFestPage()) {
        return;
    }
    var $event = $('#event');
    var previous = null;
    var draw = function () {
        $('.rate-graph.rate-graph-whole2').each(function () {
            var $area = $(this);
            var opts = $.extend(
                true,
                {},
                window.fest.getGraphOptions(previous.term, previous.teams),
                {
                    "grid": {
                        "markings": previous.markings,
                    }
                }
            );
            delete opts['colors'];
            delete opts['series'];

            var colors = window.fest.conf.useInkColor.get()
                ? {
                    alpha: "#" + previous.teams.alpha.ink,
                    bravo: "#" + previous.teams.bravo.ink,
                } : {
                    alpha: "#d9435f",
                    bravo: "#5cb85c",
                };
            var data = $.extend(true, {}, previous).data;
            for (var i = 0; i < data.length; ++i) {
                data[i].color = colors[data[i].color];
            }
            $area.empty();
            $.plot($area, data, opts);
        });
    };

    $event.on('receiveUpdateData', function (ev, data_) {
        // data.date, data.json, data.summary
        var json = data_.json;
        var $targets = $('.rate-graph.rate-graph-whole2');
        if ($targets.length < 1) {
            return;
        }

        var wins = json.wins.slice(0);
        wins.sort(function (a, b) {
            return a.at - b.at;
        });

        var scale = window.fest.getTimeBasedScaler();
        var alphaTotal = 0;
        var bravoTotal = 0;
        var alpha = [];
        var bravo = [];
        var lowerA = [];
        var upperA = [];
        var lowerB = [];
        var upperB = [];
        var markings = [];
        var lastNSStart = json.term.begin;
        var markingColor = "rgba(128,128,128,0.25)";
        for (var i = 0; i < wins.length; ++i) {
            var tmp = wins[i];
            var tmpA = tmp.alpha;
            var tmpB = tmp.bravo;
            var range;
            alphaTotal += tmpA;
            bravoTotal += tmpB;
            if (alphaTotal + bravoTotal > 0) {
                alpha.push([tmp.at * 1000, alphaTotal * 100 / (alphaTotal + bravoTotal)]);
                range = window.fest.getSignificantRange(alphaTotal, bravoTotal);
                if (range) {
                    lowerA.push([tmp.at * 1000, range[0]]);
                    upperA.push([tmp.at * 1000, range[1]]);
                } else {
                    lowerA.push([tmp.at * 1000, null]);
                    upperA.push([tmp.at * 1000, null]);
                }

                bravo.push([tmp.at * 1000, bravoTotal * 100 / (alphaTotal + bravoTotal)]);
                range = window.fest.getSignificantRange(bravoTotal, alphaTotal);
                if (range) {
                    lowerB.push([tmp.at * 1000, range[0]]);
                    upperB.push([tmp.at * 1000, range[1]]);
                } else {
                    lowerB.push([tmp.at * 1000, null]);
                    upperB.push([tmp.at * 1000, null]);
                }

               var chi = window.fest.isSignificant(alphaTotal, bravoTotal);
               if (chi === undefined || chi === 'n.s.' || chi === 'p<.10') {
                   if (lastNSStart === null) {
                       lastNSStart = tmp.at;
                   }
               } else {
                   if (lastNSStart !== null) {
                       markings.push({
                           xaxis: {
                               from: lastNSStart * 1000,
                               to: tmp.at * 1000,
                           },
                           color: markingColor,
                       });
                       lastNSStart = null;
                   }
               }
            }
        }
        if (lastNSStart !== null) {
            markings.push({
                xaxis: {
                    from: lastNSStart * 1000,
                    to: json.term.end * 1000,
                },
                color: markingColor,
            });
        }
        previous = {
            data: [
                { id: "alphaL", data: lowerA, lines: { show: true, lineWidth: 0, fill: false }, color: "alpha" },
                { id: "alphaH", data: upperA, lines: { show: true, lineWidth: 0, fill: 0.5 }, color: "alpha", fillBetween: "alphaL" },
                { id: "bravoL", data: lowerB, lines: { show: true, lineWidth: 0, fill: false }, color: "bravo" },
                { id: "bravoH", data: upperB, lines: { show: true, lineWidth: 0, fill: 0.5 }, color: "bravo", fillBetween: "bravoL" },
                // { data: alpha, lines: {show: true}, color: colorA },
                // { data: bravo, lines: {show: true}, color: colorB },
            ],
            term: json.term,
            teams: json.teams,
            markings: markings,
        };
        draw();
    });

    $event.on('updateConfigGraphType updateConfigGraphInk', function () {
        if (!previous) {
            $event.trigger('requestUpdateData');
            return;
        }
        draw();
    });
});
