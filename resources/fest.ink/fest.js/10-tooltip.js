// Copyright (C) 2015 AIZAWA Hina | MIT License
$(document).ready(function () {
    document.querySelectorAll('.auto-tooltip').forEach(function (el) {
        new bootstrap.Tooltip(el, { container: 'body' });
    });
});
