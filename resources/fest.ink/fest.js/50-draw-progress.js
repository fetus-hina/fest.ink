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
            // var date = data_.date;
            var json = data_.json;
            var summary = data_.summary;

            $('.total-progressbar').each(function () {
                var $this = $(this);
                var team = $this.attr('data-team');
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
                var color = (function () {
                    switch ($this.attr('data-team')) {
                        case 'red':
                            return json.inks.r;

                        case 'green':
                            return json.inks.g;

                        default:
                            return null;
                    }
                })();
                $this.width(
                    (rate === undefined || isNaN(rate))
                        ? '0%'
                        : ((rate * 100) + "%")
                );
                $this.css(
                    'background-color',
                    (window.fest.conf.useInkColor.get() && color !== null)
                        ? ('#' + color)
                        : ''
                );
            });
        });
    });
})(window);
