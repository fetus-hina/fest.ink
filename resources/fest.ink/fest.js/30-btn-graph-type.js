// Copyright (C) 2015 AIZAWA Hina | MIT License
(function(window) {
    "use strict";
    var $ = window.jQuery;
    $(window.document).ready(function() {
        if (!window.fest.isFestPage()) {
            return;
        }
        var festId = window.fest.getFestId();
        var $button = $('.btn-graphtype');
        var $event = $('#event');
        var onChange = function () {
            var state = window.fest.conf.graphType.get();
            $button.each(function () {
                var $this = $(this);
                $this.removeClass('btn-primary')
                    .removeClass('btn-default')
                    .addClass(
                        state === $this.attr('data-type')
                            ? 'btn-primary'
                            : 'btn-default'
                    );
            });
        };
        onChange();

        $button.click(function () {
            window.fest.conf.graphType.set(
                $(this).attr('data-type')
            );
        });

        $event.on('updateConfigGraphType', onChange);
    });
})(window);