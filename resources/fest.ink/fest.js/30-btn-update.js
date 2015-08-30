// Copyright (C) 2015 AIZAWA Hina | MIT License
(function (window) {
    "use strict";
    var $ = window.jQuery;
    $(window.document).ready(function () {
        if (!window.fest.isFestPage()) {
            return;
        }
        var festId = window.fest.getFestId();
        var $button = $('#btn-update');
        var $event = $('#event');

        $button.click(function () {
            $event.trigger('requestUpdateData');
        });

        $event.on('beginUpdateData', function () {
            $button.attr('disabled', 'disabled');
        });

        $event.on('afterUpdateData', function () {
            $button.removeAttr('disabled');
        });
    });
})(window);
