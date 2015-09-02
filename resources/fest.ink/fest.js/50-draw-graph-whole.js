// Copyright (C) 2015 AIZAWA Hina | MIT License
(function(window, undefined) {
    "use strict";
    var $ = window.jQuery;
    var NaN = Number.NaN;
    $(window.document).ready(function() {
        if (!window.fest.isFestPage()) {
            return;
        }
        var $event = $('#event');
        var previous = null;
        var draw = function () {
            $('.rate-graph.rate-graph-whole').each(function () {
                var $area = $(this);
                $area.empty();
                $.plot($area, previous.data, window.fest.getGraphOptions(previous.term, previous.inks));
            });
        };

        $event.on('receiveUpdateData', function (ev, data_) {
            // data.date, data.json, data.summary
            var json = data_.json;
            var $targets = $('.rate-graph.rate-graph-whole');
            if ($targets.length < 1) {
                return;
            }

            var wins = json.wins.slice(0);
            wins.sort(function(a, b) {
                return a.at - b.at;
            });

            var scale = window.fest.getTimeBasedScaler();
            var redTotal = 0;
            var greenTotal = 0;
            var red = [];
            var green = [];
            for (var i = 0; i < wins.length; ++i) {
                var tmp = wins[i];
                var tmpR = scale(tmp.r, tmp.at);
                var tmpG = scale(tmp.g, tmp.at);
                redTotal += tmpR;
                greenTotal += tmpG;
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
            previous = {
                data: [red, green],
                term: json.term,
                inks: json.inks,
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
})(window);
