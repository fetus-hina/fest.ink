// Copyright (C) 2015 AIZAWA Hina | MIT License
(function(window) {
    "use strict";
    var $ = window.jQuery;
    $(window.document).ready(function() {
        if (!window.fest.isFestPage()) {
            return;
        }
        var festId = window.fest.getFestId();
        var $event = $('#event');
        var isUpdating = false;
        $event.on('requestUpdateData', function () {
            if (isUpdating) {
                return;
            }
            $event.trigger('startUpdateData');
        });
        $event.on('startUpdateData', function () {
            var makeSummary = function (json) { // {{{
                var NaN = Number.NaN;
                var totalRed = 0;
                var totalGreen = 0;
                var totalRedRaw = 0;
                var totalGreenRaw = 0;
                var scale = window.fest.getTimeBasedScaler();
                for (var i = 0; i < json.wins.length; ++i) {
                    totalRed += scale(json.wins[i].r, json.wins[i].at);
                    totalGreen += scale(json.wins[i].g, json.wins[i].at);
                    totalRedRaw += json.wins[i].r;
                    totalGreenRaw += json.wins[i].g;
                }
                var totalCount = totalRed + totalGreen;
                return {
                    'r': (totalCount > 0) ? totalRed / totalCount : NaN,
                    'g': (totalCount > 0) ? totalGreen / totalCount : NaN,
                    'rSum': (totalCount > 0) ? totalRed : NaN,
                    'gSum': (totalCount > 0) ? totalGreen : NaN,
                    'rSumRaw': (totalCount > 0) ? totalRedRaw: NaN,
                    'gSumRaw': (totalCount > 0) ? totalGreenRaw: NaN
                };
            }; // }}}
            $event.trigger('beginUpdateData');
            var date = new Date();
            $.getJSON(
                '/' + encodeURIComponent(festId) + '.json',
                { _: Math.floor(date / 1000) },
                function (json) {
                    $event.trigger('receiveUpdateData', {
                        date: date,
                        json: json,
                        summary: makeSummary(json),
                    });
                    $event.trigger('afterUpdateData');
                    isUpdating = false;
                }
            );
        });
    });
})(window);
