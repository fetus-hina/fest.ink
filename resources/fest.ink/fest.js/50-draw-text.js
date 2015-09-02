// Copyright (C) 2015 AIZAWA Hina | MIT License
$(document).ready(function () {
    if (!window.fest.isFestPage()) {
        return;
    }
    var NaN = Number.NaN;
    var $event = $('#event');
    $event.on('receiveUpdateData', function (ev, data_) {
        // data.date, data.json, data.summary
        var date = data_.date;
        var json = data_.json;
        var summary = data_.summary;

        // JSON に含まれるタイムスタンプの最大値(int)を取得
        var lastUpdatedTimestamp = Math.max.apply(
            null,
            json.wins.map(function (value) {
                return value.at;
            })
        );

        var createDate = function (millisec) {
            return new timezoneJS.Date(
                millisec,
                window.fest.conf.timezone.get()
            );
        };

        $('.total-rate').each(function () { // {{{
            var $this = $(this);
            var rate = (function () {
                switch ($this.attr('data-team')) {
                    case 'red':
                        return summary.r;

                    case 'green':
                        return summary.g;

                    default:
                        return NaN;
                }
            })();
            $this.text(
                (rate === undefined || isNaN(rate))
                    ? '???'
                    : ((rate * 100).toFixed(1) + '%')
            );
        }); // }}}
        $('.sample-count').text(
            (isNaN(summary.rSumRaw) || isNaN(summary.gSumRaw))
                ? '???'
                : window.fest.numberFormat(summary.rSumRaw + summary.gSumRaw)
        );
        $('.last-updated-at').text(
            lastUpdatedTimestamp < 1 || lastUpdatedTimestamp === undefined || isNaN(lastUpdatedTimestamp)
                ? '???'
                : window.fest.dateTimeFormat(
                    createDate(lastUpdatedTimestamp * 1000)
                )
        );

        $('.last-fetched-at').text(
            window.fest.dateTimeFormat(
                createDate(date.getTime())
            )
        );
    });
});
