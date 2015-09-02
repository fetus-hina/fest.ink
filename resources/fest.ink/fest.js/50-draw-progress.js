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
        var colors = null;
        var updateColor = function () {
            $('.total-progressbar').each(function () {
                var $this = $(this);
                var color = colors[$this.attr('data-team') === 'red' ? 0 : 1];
                $this.css(
                    'background-color',
                    (window.fest.conf.useInkColor.get() && color !== null)
                        ? ('#' + color)
                        : ''
                );
            });
        };

        $event.on('receiveUpdateData', function (ev, data_) {
            var json = data_.json;
            var summary = data_.summary;
            colors = [json.inks.r, json.inks.g];
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
                $this.width(
                    (rate === undefined || isNaN(rate))
                        ? '0%'
                        : ((rate * 100) + "%")
                );
            });
            updateColor();
        });
        $event.on('updateConfigGraphInk', function () {
            if (!colors) {
                $event.trigger('requestUpdateData');
                return;
            }
            updateColor();
        });
    });
})(window);
