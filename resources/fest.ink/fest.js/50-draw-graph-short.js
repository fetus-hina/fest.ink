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
        $event.on('receiveUpdateData', function (ev, data_) {
            // data.date, data.json, data.summary
            var json = data_.json;
            var $targets = $('.rate-graph.rate-graph-short');
            if ($targets.length < 1) {
                return;
            }

            var scale = window.fest.getTimeBasedScaler();
            var red = [];
            var green = [];
            for (var i = 0; i < json.wins.length; ++i) {
                var tmp = json.wins[i];
                var tmpR = scale(tmp.r, tmp.at);
                var tmpG = scale(tmp.g, tmp.at);
                var sum = tmpR + tmpG;
                if (sum > 0) {
                    red.push([tmp.at * 1000, tmpR * 100 / sum]);
                    green.push([tmp.at * 1000, tmpG * 100 / sum]);
                }
            }

            $targets.each(function () {
                var $area = $(this);
                $area.empty();
                $.plot($area, [red, green], window.fest.getGraphOptions(json.term, json.inks));
            });
        });
    });
})(window);
