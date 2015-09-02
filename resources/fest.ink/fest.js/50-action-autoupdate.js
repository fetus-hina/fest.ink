// Copyright (C) 2015 AIZAWA Hina | MIT License
(function(window) {
    var $ = window.jQuery;
    $(window.document).ready(function() {
        if (!window.fest.isFestPage()) {
            return;
        }
        var $event = $('#event');
        var currentInterval = null;
        var currentTimerId = null;
        var updateByTimer = false;

        var stopCurrentAutoUpdate = function () {
            if (currentTimerId !== null) {
                currentTimerId = window.clearInterval(currentTimerId);
            }
            currentInterval = null;
            currentTimerId = null;
            updateByTimer = false;
        };

        var startAutoUpdate = function () {
            currentInterval = window.fest.conf.updateInterval.get();
            currentTimerId = window.setInterval(
                function () {
                    updateByTimer = true;
                    $event.trigger('requestUpdateData');
                },
                currentInterval
            );
        };

        // 自動更新が開始・終了または時間の変更が行われた時
        $event.on('updateConfigAutoUpdate updateConfigUpdateInterval', function () {
            if (!window.fest.conf.autoUpdate.get()) {
                // 停止が求められている時
                if (currentTimerId !== null) {
                    stopCurrentAutoUpdate();
                }
                return;
            } else {
                // 開始が求められているか時間が変更されている時
                if (currentTimerId !== null) {
                    // インターバルが現在動作中のものと同じであれば特に何もする必要はない
                    if (currentInterval === window.fest.conf.updateInterval.get()) {
                        return;
                    }
                }

                stopCurrentAutoUpdate();
                startAutoUpdate();
                $event.trigger('requestUpdateData');
            }
        });

        // 更新開始時
        $event.on('beginUpdateData', function () {
            if (updateByTimer &&
                    window.fest.conf.autoUpdate.get() &&
                    window.fest.conf.updateInterval.get() === currentInterval
            ) {
                // 自分で開始した更新なら特に何かをする必要はない
                return;
            }

            // 他人が開始した更新か設定が変更されていたらやり直す
            stopCurrentAutoUpdate();
            if (window.fest.conf.autoUpdate.get()) {
                startAutoUpdate();
            }
        });

        // 更新終了時に自分で開始したことを示すフラグを落とす
        $event.on('afterUpdateData', function () {
            updateByTimer = false;
        });
    });
})(window);
