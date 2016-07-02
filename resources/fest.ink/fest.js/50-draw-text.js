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
            var teamKey = ($this.attr('data-team') + "").substr(0, 1) + "Range";
            var rate = (summary[teamKey] && summary[teamKey].min && summary[teamKey].max)
                    ? summary[teamKey]
                    : undefined;
            if (rate === undefined) {
                $this.empty().text('???');
            } else {
                $this.empty().text(
                    (parseFloat(rate.min).toFixed(1)) +
                    '～' +
                    (parseFloat(rate.max).toFixed(1)) +
                    '%'
                );
            }
        }); // }}}
        $('.total-rate-info').each(function () {
            var $this = $(this);
            var chi2 = window.fest.isSignificant(summary.aSumRaw, summary.bSumRaw);
            $this.text((function() {
                var teamName = (summary.aSumRaw > summary.bSumRaw)
                        ? json.teams.alpha.name
                        : json.teams.bravo.name;
                switch (chi2) {
                    case 'n.s.':
                        return '【優劣不明】';

                    case 'p<.05':
                        return '【' + teamName + 'チーム優勢?】';

                    case 'p<.01':
                        return '【' + teamName + 'チーム優勢の模様】';

                    default:
                        return '';
                }
            })());
        });
        $('.sample-count').each(function () { // {{{
            var $this = $(this);
            if (isNaN(summary.aSumRaw) || isNaN(summary.bSumRaw)) {
                $this.text('???');
                return;
            }

            switch ($this.attr('data-team')) {
                case 'alpha':
                    $this.text(window.fest.numberFormat(summary.aSumRaw));
                    break;

                case 'bravo':
                    $this.text(window.fest.numberFormat(summary.bSumRaw));
                    break;

                default:
                    $this.text(
                        window.fest.numberFormat(summary.aSumRaw + summary.bSumRaw)
                    );
                    break;
            }
        }); // }}}
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
