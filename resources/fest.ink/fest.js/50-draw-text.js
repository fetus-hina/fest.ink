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
            var teamId = ($this.attr('data-team') + "").substr(0, 1);
            var rate = summary[teamId] ? summary[teamId] : NaN;
            if (rate === undefined || isNaN(rate)) {
                $this.empty().text('???');
            } else {
                var range = window.fest.conf.useGraphScale.get()
                    ? undefined
                    : window.fest.getSignificantRange(summary.aSumRaw, summary.bSumRaw);
                $this.empty().append(
                    (rate * 100).toFixed(1)
                );
                if (range) {
                    $this.append(
                        $('<span>')
                            .text('±' + ((range[1] - range[0]) / 2).toFixed(1))
                            .css({
                                color: '#999',
                                fontSize: '0.618em',
                            })
                    );
                }
                $this.append('%');
            }
        }); // }}}
        $('.total-rate-info').each(function () {
            var $this = $(this);
            var chi2 = window.fest.isSignificant(summary.aSumRaw, summary.bSumRaw);
            $this.text(
                chi2 === 'n.s.'
                    ? '（有意差なし）'
                    : ''
            );
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
