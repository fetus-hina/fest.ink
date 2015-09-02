// Copyright (C) 2015 AIZAWA Hina | MIT License
(function(window) {
    var $ = window.jQuery;
    $(window.document).ready(function () {
        if (!window.fest.isFestPage()) {
            return;
        }
        window.setTimeout(function () {
            $('#event').trigger('requestUpdateData');
        }, 1);
    });
})(window);
