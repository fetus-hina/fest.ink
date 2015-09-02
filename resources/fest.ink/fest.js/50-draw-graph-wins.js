// Copyright (C) 2015 AIZAWA Hina | MIT License
$(document).ready(function () {
    if (!window.fest.isFestPage()) {
        return;
    }
    var $event = $('#event');
    var previous = null;
    var draw = function () {
        var options = window.fest.getGraphOptions(previous.term, previous.inks);
        options.series.stack = false;

        $('.rate-graph.rate-graph-win-count').each(function () {
            var $area = $(this);
            $area.empty();
            $.plot($area, previous.data, options);
        });
    };

    $event.on('receiveUpdateData', function (ev, data_) {
        // data.date, data.json, data.summary
        var json = data_.json;
        var $targets = $('.rate-graph.rate-graph-win-count');
        if ($targets.length < 1) {
            return;
        }

        var wins = json.wins.slice(0);
        wins.sort(function (a, b) {
            return a.at - b.at;
        });

        var scale = window.fest.getTimeBasedScaler();
        var redTotal = 0;
        var greenTotal = 0;
        var red = [];
        var green = [];
        for (var i = 0; i < wins.length; ++i) {
            var tmp = wins[i];
            redTotal += scale(tmp.r, tmp.at);
            greenTotal += scale(tmp.g, tmp.at);
            if (redTotal + greenTotal > 0) {
                red.push([tmp.at * 1000, redTotal]);
                green.push([tmp.at * 1000, greenTotal]);
            }
        }
        var maxTotal = Math.max(redTotal, greenTotal);
        if (maxTotal < 1) {
            maxTotal = 1;
        }

        var toPercentage = function (val) {
            val[1] = val[1] * 100 / maxTotal;
            return val;
        };

        previous = {
            data: [red.map(toPercentage), green.map(toPercentage)],
            term: json.term,
            inks: json.inks,
        };
        draw();
    });

    $event.on('updateConfigGraphInk', function () {
        if (!previous) {
            $event.trigger('requestUpdateData');
            return;
        }
        draw();
    });
});
