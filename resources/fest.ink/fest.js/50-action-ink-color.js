// Copyright (C) 2015 AIZAWA Hina | MIT License
(function(window) {
    "use strict";
    var $ = window.jQuery;
    $(window.document).ready(function() {
        if (!window.fest.isFestPage()) {
            return;
        }
        var $event = $('#event');
        $event.on('updateConfigGraphInk', function () {
            $event.trigger('requestUpdateData');
        });
    });
})(window);
