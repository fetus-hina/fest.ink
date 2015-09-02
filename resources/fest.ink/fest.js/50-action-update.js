// Copyright (C) 2015 AIZAWA Hina | MIT License
$(document).ready(function () {
    if (!window.fest.isFestPage()) {
        return;
    }
    var festId = window.fest.getFestId();
    var $event = $('#event');
    var isUpdating = false;
    var previous = null;
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
    $event.on('requestUpdateData', function () {
        if (isUpdating) {
            return;
        }
        $event.trigger('startUpdateData');
    });
    $event.on('requestRetriggerUpdateEvent', function () {
        if (!previous) {
            $event.trigger('requestUpdateData');
            return;
        }
        if (isUpdating) {
            return;
        }
        $event.trigger('beginUpdateData');
        $event.trigger('receiveUpdateData', {
            date: previous.date,
            json: previous.json,
            summary: makeSummary(previous.json),
        });
        $event.trigger('afterUpdateData');
    });
    $event.on('startUpdateData', function () {
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
                previous = {
                    date: date,
                    json: json,
                };
                isUpdating = false;
            }
        );
    });
});
