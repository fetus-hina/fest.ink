(function(window) {
    var $ = window.jQuery;
    var document = window.document;
    $(document).ready(function() {
        $(document.body).append(
            $('<script>')
                .attr('src', '//platform.twitter.com/widgets.js')
                .attr('async', 'async')
        );
    });
})(window);
